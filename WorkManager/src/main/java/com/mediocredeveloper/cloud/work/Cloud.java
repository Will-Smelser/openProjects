package com.mediocredeveloper.cloud.work;

import com.hazelcast.core.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.Map;
import java.util.Set;
import java.util.concurrent.*;

/**
 * Created by Will2 on 11/16/2016.
 */
public class Cloud<T>{

    private static final Logger LOGGER = LoggerFactory.getLogger(Cloud.class);

    private static final String REGISTRY_NAME = "%s_REGISTRY";
    private static final String TOPIC = "%s_SHARED_WORK";
    private static final String ELECT_QUEUE = "%s_ELECT_QUEUE";
    private static final String WORK_QUEUE = "%s_WORK_QUEUE";

    /**
     * All queues and maps get a names based on this group.
     */
    private final String group;

    /**
     * A unique name for this Cloud instance
     */
    private final String name;

    /**
     * Whether this is the master
     */
    private volatile boolean isMaster = false;

    /**
     * Reference to the hazelcast instance
     */
    private final HazelcastInstance hcast;

    /**
     * The provided worker for work queue
     */
    private final CloudWorker<T> worker;

    /**
     * All Cloud classes register themselves with this map.
     */
    private final Set<String> registry;

    /**
     * If we need to elect a master, this queue is listened to
     */
    private final BlockingQueue<Object> electQueue;

    /**
     * For inter node messaging.  You can message any node in the registry
     */
    private final BlockingQueue<Message<CloudMessage<T>>> msgQueue = new ArrayBlockingQueue<>(1000);

    /**
     * The shared work queue.  Only one member will perform work on this queue.
     */
    private final BlockingQueue<T> workQueue;

    /**
     * All instances listen to messages on this topic.
     */
    private final ITopic messageTopic;

    //we keep reusing these threads, so this will save overhead of recreating threads
    private final ExecutorService electionPool =  Executors.newSingleThreadExecutor();
    private final ExecutorService workerPool =  Executors.newSingleThreadExecutor();

    /**
     * Create a Cloud manager to facilitate some basic cluster stuff.
     * @param group A unique name that a group of hosts want to comunicate under.
     * @param name
     * @param hcast
     * @param worker
     */
    public Cloud(final String group, final String name, HazelcastInstance hcast, final CloudWorker<T> worker) throws InterruptedException {
        this.group = group;
        this.name = name;
        this.hcast = hcast;
        this.worker = worker;
        this.registry = hcast.getSet(String.format(REGISTRY_NAME,group));
        this.electQueue = hcast.getQueue(String.format(ELECT_QUEUE,group));
        this.workQueue = hcast.getQueue(String.format(WORK_QUEUE,name,group));

        //begin waiting to see if master
        listenForElection();

        //listen for work on the work queue
        listenForWork();

        //register
        registry.add(name);

        //create the topic
        messageTopic = hcast.getReliableTopic(String.format(TOPIC,group));

        //add listener to shared work topic
        messageTopic.addMessageListener(new MessageListener<CloudMessage<T>>() {
            @Override
            public void onMessage(Message<CloudMessage<T>> message) {
                CloudMessage<T> msg = message.getMessageObject();
                if (msg.getTo().equals(name) || name.matches(msg.getTo())) {
                    if(msg.getSysMessage() != null) {
                        msgQueue.add(message);
                    }else{
                        handleSysMessage(message);
                    }
                }
            }
        });
    }

    /**
     * Hold an election for a master.  By default there are no masters, if you would like a master node,
     * then you must call elect.
     */
    public final void elect(){
        electQueue.add(1L);
    }


    /**
     * If this instance is currently the master
     * @return
     */
    public final boolean isMaster(){
        return isMaster;
    }

    /**
     * Wait on a message.
     * @return
     * @throws InterruptedException
     */
    public final synchronized CloudMessage<T> read() throws InterruptedException {
        return msgQueue.take().getMessageObject();
    }

    /**
     * Send a message to another host.  Would be good to JMS Message
     * @param to
     * @param message
     * @throws InterruptedException
     */
    public final void message(String to, T message) throws InterruptedException, CloudMsgError {
        if(!registry.contains(to)){
            throw new CloudMsgError("No Recipient: "+to);
        }
        messageTopic.publish(new CloudMessage<T>(name, to, message));
    }

    /**
     * Post work to be done
     * @param work
     */
    public final void post(T work){
        workQueue.add(work);
    }

    private void handleSysMessage(Message<CloudMessage<T>> message){
        String action = message.getMessageObject().getSysMessage();
        //TODO: do something
    }

    /**
     * wait on the election queue for a message. I get the message, I am the master!
     */
    private final void listenForElection(){
        Runnable runner = new Runnable() {
            public void run() {
                try {
                    electQueue.take();
                    isMaster = true;
                } catch (InterruptedException e) {
                    LOGGER.error("Failed while waiting on election queue.", e);
                } finally {
                    listenForElection();
                }
            }
        };
        electionPool.submit(runner);
    }

    private final void listenForWork(){
        Runnable runner = new Runnable() {
            @Override
            public void run() {
                try {
                    T work = workQueue.take();
                    worker.doWork(work);
                }catch(InterruptedException e){
                    LOGGER.error("Failed while waiting on work queue.", e);
                }finally {
                    listenForWork();
                }
            }
        };
        workerPool.submit(runner);
    }

}
