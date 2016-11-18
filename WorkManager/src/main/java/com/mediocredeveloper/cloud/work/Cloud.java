package com.mediocredeveloper.cloud.work;

import com.hazelcast.core.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.List;
import java.util.Map;
import java.util.concurrent.BlockingQueue;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.CopyOnWriteArrayList;

/**
 * Created by Will2 on 11/16/2016.
 */
public class Cloud<T>{

    private static final Logger LOGGER = LoggerFactory.getLogger(Cloud.class);

    private static final String REGISTY_NAME = "%s_REGISTRY";
    private static final String TOPIC = "%s_SHARED_WORK";
    private static final String MASTER_QUEUE = "%s_MASTER_QUEUE";
    private static final String ELECT_QUEUE = "%s_ELECT_QUEUE";

    private final String group;

    private final String name;

    private volatile boolean isMaster = false;

    private HazelcastInstance hcast;

    private Map<String, Object> registry;

    private BlockingQueue<Object> electQueue;

    private BlockingQueue<CloudMessage<T>> msgQueue;

    //topic that multiple queues work
    private ITopic sharedWorkTopic;

    /**
     * Create a Cloud manager to facilitate some basic cluster stuff.
     * @param group A unique name that a group of hosts want to comunicate under.
     * @param name
     * @param hcast
     * @param sharedWork
     */
    public Cloud(String group, String name, HazelcastInstance hcast, final CloudWorker<T> sharedWork){
        this.group = group;
        this.name = name;
        this.hcast = hcast;
        this.registry = hcast.getMap(String.format(REGISTY_NAME,group));
        this.electQueue = hcast.getQueue(String.format(ELECT_QUEUE,group));
        this.msgQueue = hcast.getQueue(String.format(name,group));

        //begin waiting to see if master
        listenForElection();

        //register
        registry.put(name, 1L);

        //create the topic
        sharedWorkTopic = hcast.getReliableTopic(String.format(TOPIC,group));

        //add listener to shared work topic
        sharedWorkTopic.addMessageListener(new MessageListener<T>() {
            @Override
            public void onMessage(Message<T> message) {
                sharedWork.doWork(message.getMessageObject());
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
    private final CloudMessage<T> read() throws InterruptedException {
        return msgQueue.take();
    }

    /**
     * Send a message to another host.  Would be good to JMS Message
     * @param to
     * @param message
     * @throws InterruptedException
     */
    private final void message(String to, T message) throws InterruptedException, CloudMsgError {
        if(registry.get(to) == null){
            throw new CloudMsgError("No Recipient: "+to);
        }
        hcast.getQueue(to).put(new CloudMessage<T>(name, message));
    }

    /**
     * Post work to be done
     * @param work
     */
    private final void post(T work){
        sharedWorkTopic.publish(work);
    }

    /**
     * wait on the election queue for a message. I get the message, I am the master!
     */
    private final void listenForElection(){
        new Thread(new Runnable() {
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
        }).start();
    }

}
