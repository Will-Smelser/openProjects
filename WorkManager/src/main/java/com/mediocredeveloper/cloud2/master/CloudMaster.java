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
import java.util.concurrent.locks.ReentrantLock;

/**
 * Created by Will2 on 12/3/2016.
 */
public class CloudMaster {

    private static final Logger LOGGER = LoggerFactory.getLogger(CloudMaster.class);

    private static final String LOCK_NAME = "%s_ELECT_LOCK";
    private static final String ELECT_QUEUE = "%s_ELECT_QUEUE";

    private static final String WAIT_FAILURE_LOCK = "%s_MASTER_FAIL_LOCK";

    private final String group;
    private final CloudRegistry registry;
    private final HazelcastInstance hcast;
    private final CloudMasterEventHandler handler;

    private final Lock failLock;
    private final Lock lock;
    private final Semaphore waitElectLock  = new Semaphore(1);
    private final BlockingQueue<Object> electQueue;

    private final CloudMessageServicer<CloudMasterAction, CloudResp> msgService;

    private volatile boolean isMaster = false;

    private final ExecutorService electionPool =  Executors.newSingleThreadExecutor();
    private final ExecutorService waitOnFailurePool =  Executors.newSingleThreadExecutor();

    public CloudMaster(CloudRegistry registry, HazelcastInstance hcast, CloudMasterEventHandler handler) {
        this.group = registry.getGroup();
        this.registry = registry;
        this.hcast = hcast;
        this.handler = handler;

        CloudContext.register(registry.getGroup(), registry.getName(), CloudMaster.class, this);

        this.failLock = hcast.getLock(String.format(WAIT_FAILURE_LOCK,group));

        this.lock = hcast.getLock(String.format(LOCK_NAME, group));
        this.electQueue = hcast.getQueue(String.format(ELECT_QUEUE, group));

        String name = this.registry.getName();
        CloudMasterElectListener listener = new CloudMasterElectListener(group, name);
        msgService = new CloudMessageServicer<>(group, name, hcast, listener);

        listenForElection();
    }

    void isMaster(boolean isMaster){
        if(this.isMaster != isMaster){
            handler.handle(isMaster ? CloudMasterEvent.ELECTED : CloudMasterEvent.DEMOTED);
        }
        this.isMaster = isMaster;
    }

    boolean isMaster(){
        return isMaster;
    }

    /**
     * This is just for testing.
     */
    void waitIfElection() {
        try {
            waitElectLock.acquire();
        }catch(InterruptedException e){
            LOGGER.error("Error waiting on election to complete.", e);
        }finally {
            waitElectLock.release();
        }
    }

    /**
     * Elect a new Master node.  This will first tell all nodes to stop being master.  Since we don't actually track which
     * node is master, we have to send a message to all.
     * @param time Total time to wait on message telling all nodes to stop being master prior to forcing an election.
     * @param unit The time unit for the {@param time} parameter.
     * @throws CloudMessageError
     */
    public void elect(long time, TimeUnit unit) throws CloudMessageError {
        //only allow one election at a time
        lock.lock();
        try {
            this.waitElectLock.acquire();

            //make sure there no longer is a master
            sendAll(CloudMasterAction.NO_MASTER, time, unit);

            electQueue.put("elect");

        }catch(Exception e){
            //make sure we don't hold this
            this.waitElectLock.release();

            throw new CloudMessageError("Error notifing all nodes there is going to be a new master", e);
        }finally{
            lock.unlock();
        }

        try {
            this.waitElectLock.acquire(); //have to wait on the election to be completed
        } catch (InterruptedException e) {
            LOGGER.error("Interrupted while waiting for election to complete.", e);
        }finally {
            this.waitElectLock.release(); //release the lock again
        }

    }

    void releaseWaitElectionComplete(){
        this.waitElectLock.release();
    }

    /**
     * Send a message to all nodes within the registry.  Then wait on them to all respond.
     * @param action
     * @param time
     * @param unit
     * @throws CloudMessageError
     */
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
                throw new CloudMessageError("Error waiting on all registered nodes to respond.",e);
            }
        }
    }

    private final void listenForElection(){
        Runnable runner = new Runnable() {
            public void run() {
                try {
                    electQueue.take();
                    isMaster = true;
                    failLock.lock();

                    handler.handle(CloudMasterEvent.ELECTED);

                    sendAll(CloudMasterAction.WAIT_ON_FAILURE, 30, TimeUnit.SECONDS);
                    sendAll(CloudMasterAction.ELECTION_COMPLETE, 30, TimeUnit.SECONDS);

                } catch (InterruptedException e) {
                    LOGGER.error("Failed while waiting on election queue.", e);
                } catch (CloudMessageError e) {
                    LOGGER.error("Failed sending message to nodes to wait for master failure", e);
                } finally {
                    listenForElection();
                }
            }
        };
        electionPool.submit(runner);
    }

    /**
     * Try to aquire the lock held by master.  Hazelcast will force this lock to release if the node goes down.
     */
    final void waitForFailure(){
        Runnable runner = new Runnable() {
            @Override
            public void run() {
                System.out.println(">>>About to get failure lock");
                failLock.lock();
                try {
                    System.out.println(">>>About to hold election");
                    elect(30, TimeUnit.SECONDS);
                } catch (CloudMessageError e) {
                    LOGGER.error("Error electing a back master after failure detected.", e);
                } finally {
                    System.out.println(">>>>Election complete");
                    failLock.unlock();
                }
            }
        };
        waitOnFailurePool.submit(runner);
    }
}
