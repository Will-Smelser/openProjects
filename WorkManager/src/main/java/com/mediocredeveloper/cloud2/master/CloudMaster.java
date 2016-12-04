package com.mediocredeveloper.cloud2.master;

import com.hazelcast.core.HazelcastInstance;
import com.mediocredeveloper.cloud2.CloudContext;
import com.mediocredeveloper.cloud2.message.CloudMessageError;
import com.mediocredeveloper.cloud2.message.CloudMessageServicer;
import com.mediocredeveloper.cloud2.message.CloudResp;
import com.mediocredeveloper.cloud2.registry.CloudRegistry;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import javax.naming.Context;
import javax.naming.NamingException;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.*;
import java.util.concurrent.locks.Lock;

/**
 * Created by Will2 on 12/3/2016.
 */
public class CloudMaster {

    private static final Logger LOGGER = LoggerFactory.getLogger(CloudMaster.class);

    private static final String LOCK_NAME = "%s_ELECT_LOCK";
    private static final String ELECT_QUEUE = "%s_ELECT_QUEUE";

    private final String group;
    private final CloudRegistry registry;
    private final HazelcastInstance hcast;
    private final CloudMasterEventHandler handler;

    private final Lock lock;
    private final BlockingQueue<Object> electQueue;

    private final CloudMessageServicer<CloudMasterAction, CloudResp> msgService;

    private volatile boolean isMaster = false;

    private final ExecutorService electionPool =  Executors.newSingleThreadExecutor();

    public CloudMaster(CloudRegistry registry, HazelcastInstance hcast, CloudMasterEventHandler handler) {
        this.group = registry.getGroup();
        this.registry = registry;
        this.hcast = hcast;
        this.handler = handler;

        CloudContext.register(registry.getGroup(), registry.getName(), CloudMaster.class, this);

        this.lock = hcast.getLock(String.format(LOCK_NAME, group));
        this.electQueue = hcast.getQueue(String.format(ELECT_QUEUE, group));

        String name = this.registry.getName();
        CloudMasterElectListener listener = new CloudMasterElectListener(group, name);
        msgService = new CloudMessageServicer<>(group, name, hcast, listener);

        listenForElection();
    }

    synchronized void isMaster(boolean isMaster){
        if(this.isMaster != isMaster){
            handler.handle(isMaster ? CloudMasterEvent.ELECTED : CloudMasterEvent.DEMOTED);
        }
        this.isMaster = isMaster;
    }

    boolean isMaster(){
        return isMaster;
    }

    public void elect(long time, TimeUnit unit) throws CloudMessageError {
        //only allow one election at a time
        lock.lock();
        try {
            sendAll(CloudMasterAction.NO_MASTER, time, unit);
            electQueue.put("elect");
        }catch(InterruptedException e){
            throw new CloudMessageError("Error notifing all nodes there is going to be a new master", e);
        }finally{
            lock.unlock();
        }
    }

    private void sendAll(CloudMasterAction action, long time, TimeUnit unit) throws CloudMessageError {
        List<Future<CloudResp>> messages = new ArrayList<>();
        for(String name : registry.getRegistered()){
            messages.add(msgService.send(name, action));
        }

        long current, start = System.currentTimeMillis();
        long totalTimeInMillis = unit.toMillis(time);

        for(Future<CloudResp> resp : messages){
            current = System.currentTimeMillis();
            long remaining = totalTimeInMillis - (current-start);
            try {
                resp.get(remaining, TimeUnit.MILLISECONDS);
            } catch (InterruptedException|ExecutionException|TimeoutException e) {
                throw new CloudMessageError("Error waiting on sending message to all nodes in registry.",e);
            }
        }
    }

    private final void listenForElection(){
        Runnable runner = new Runnable() {
            public void run() {
                try {
                    electQueue.take();
                    isMaster = true;
                    handler.handle(CloudMasterEvent.ELECTED);
                } catch (InterruptedException e) {
                    LOGGER.error("Failed while waiting on election queue.", e);
                } finally {
                    listenForElection();
                }
            }
        };
        electionPool.submit(runner);
    }
}
