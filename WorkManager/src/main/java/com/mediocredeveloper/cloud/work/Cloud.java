package com.mediocredeveloper.cloud.work;

import com.hazelcast.core.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.Map;
import java.util.concurrent.*;

/**
 * Created by Will2 on 11/16/2016.
 */
public class Cloud<T>{

    private static final Logger LOGGER = LoggerFactory.getLogger(Cloud.class);

    private static final String REGISTY_NAME = "%s_REGISTRY";
    private static final String TOPIC = "%s_SHARED_WORK";
    private static final String MASTER_QUEUE = "%s_MASTER_QUEUE";
    private static final String ELECT_QUEUE = "%s_ELECT_QUEUE";
    private static final String WORK_QUEUE = "%s_WORK_QUEUE";

    private final String group;

    private final String name;

    private volatile boolean isMaster = false;

    private final HazelcastInstance hcast;

    private final CloudWorker<T> worker;

    private final Map<String, Object> registry;

    private final BlockingQueue<Object> electQueue;

    private final BlockingQueue<Message<CloudMessage<T>>> msgQueue = new ArrayBlockingQueue<>(1000);

    private final BlockingQueue<T> workQueue;

    //topic that multiple queues work
    private final ITopic messageTopic;

    private final ExecutorService electionPool =  Executors.newSingleThreadExecutor();

    private final ExecutorService workerPool =  Executors.newSingleThreadExecutor();

    /**
     * Create a Cloud manager to facilitate some basic cluster stuff.
     * @param group A unique name that a group of hosts want to comunicate under.
     * @param name
     * @param hcast
     * @param worker
     */
    public Cloud(final String group, final String name, HazelcastInstance hcast, final CloudWorker<T> worker){
        this.group = group;
        this.name = name;
        this.hcast = hcast;
        this.worker = worker;
        this.registry = hcast.getMap(String.format(REGISTY_NAME,group));
        this.electQueue = hcast.getQueue(String.format(ELECT_QUEUE,group));
        this.workQueue = hcast.getQueue(String.format(WORK_QUEUE,name,group));

        //begin waiting to see if master
        listenForElection();

        //listen for work on the work queue
        listenForWork();

        //register
        registry.put(name, 1L);

        //create the topic
        messageTopic = hcast.getReliableTopic(String.format(TOPIC,group));

        //add listener to shared work topic
        messageTopic.addMessageListener(new MessageListener<CloudMessage<T>>() {
            @Override
            public void onMessage(Message<CloudMessage<T>> message) {
                CloudMessage<T> msg = message.getMessageObject();
                if (msg.getTo().equals(name) || name.matches(msg.getTo())) {
                    msgQueue.add(message);
                }
            }
        });

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
        if(registry.get(to) == null){
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
