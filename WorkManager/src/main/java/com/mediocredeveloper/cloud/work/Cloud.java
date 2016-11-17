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

    private static final String REGISTY_NAME = "REGISTRY";
    private static final String TOPIC = "SHARED_WORK";
    private static final String MASTER_QUEUE = "MASTER_QUEUE";
    private static final String ELECT_QUEUE = "ELECT_QUEUE";

    private final String name;

    private volatile boolean isMaster = false;

    private HazelcastInstance hcast;

    private Map<String, Object> registry;

    private BlockingQueue<Object> electQueue;

    private BlockingQueue<CloudMessage<T>> msgQueue;

    private ITopic sharedWorkTopic;

    public Cloud(String name, HazelcastInstance hcast, final CloudWorker<T> sharedWork){
        this.name = name;
        this.hcast = hcast;
        this.registry = hcast.getMap(REGISTY_NAME);
        this.electQueue = hcast.getQueue(ELECT_QUEUE);
        this.msgQueue = hcast.getQueue(name);

        //begin waiting to see if master
        listenForElection();

        //register
        registry.put(name, 1L);

        //create the topic
        sharedWorkTopic = hcast.getReliableTopic(TOPIC);

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
    private final void send(String to, T message) throws InterruptedException, CloudMsgError {
        if(registry.get(to) == null){
            throw new CloudMsgError("No Recipient: "+to);
        }
        hcast.getQueue(to).put(new CloudMessage<T>(name, message));
    }

    /**
     * wait on the election queue for a message. I get the message, I am the master!
     */
    private final void listenForElection(){
        new Thread(new Runnable() {
            public void run() {
                try {
                    Object waitOnElection = electQueue.take();
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
