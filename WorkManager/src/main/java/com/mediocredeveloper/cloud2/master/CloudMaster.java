package com.mediocredeveloper.cloud2.master;

import com.hazelcast.core.HazelcastInstance;
import com.hazelcast.core.ReplicatedMap;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.concurrent.*;
import java.util.concurrent.atomic.AtomicBoolean;
import java.util.concurrent.locks.Lock;

/**
 * Created by Will2 on 12/10/2016.
 *
 * TODO: need a mechanism to shutdown current master without starting another.
 */
public class CloudMaster {
    private static final Logger LOGGER = LoggerFactory.getLogger(CloudMaster.class);

    private static final String MASTER_LOCK_NAME = "%s_MASTER_LOCK";
    private static final String LOCK_NAME = "%s_ELECT_LOCK";
    private static final String LOCK_NAME_COMPLETE = "%s_ELECT_LOCK_COMPLETE";
    private static final String ELECT_QUEUE = "%s_ELECT_QUEUE";
    private static final String MASTER = "%s_MASTER_MAP";

    private final String name;
    private final String group;

    private final AtomicBoolean isMaster = new AtomicBoolean(false);

    private final Lock masterLock;
    private final Lock electionLock;
    private final BlockingQueue<Object> electComplete;
    private final BlockingQueue<Object> electQueue;

    private static final String MASTER_KEY = "master";
    private final ReplicatedMap<String, String> masterMap;

    private final CloudMasterEventHandler handler;

    private final ExecutorService electionPool =  Executors.newSingleThreadExecutor();
    private final ExecutorService masterChangedPool = Executors.newSingleThreadExecutor();
    private final ExecutorService masterLostPool =  Executors.newSingleThreadExecutor();

    public CloudMaster(String group, String name, HazelcastInstance hcast, CloudMasterEventHandler handler){

        this.masterLock = hcast.getLock(String.format(MASTER_LOCK_NAME, group));
        this.electionLock = hcast.getLock(String.format(LOCK_NAME, group));
        this.electComplete = hcast.getQueue(String.format(LOCK_NAME_COMPLETE, group));
        this.electQueue = hcast.getQueue(String.format(ELECT_QUEUE, group));
        this.masterMap = hcast.getReplicatedMap(String.format(MASTER, group));

        this.group = group;
        this.name = name;

        this.handler = handler;

        listenForStateChanged();
        listenForElection();
        listenForMasterLost();
    }

    public boolean isMaster(){
        return this.isMaster.get();
    }

    public void elect() {
        //only 1 election at a time
        if(electionLock.tryLock()){
            try{
                this.electQueue.add("election");

            } catch (Exception e) {
                LOGGER.error("Failed sending message to current master, holdin election anyway",e);
            } finally{
                electionLock.unlock();

                //the winner of election should release electionLock
                try {
                    electComplete.take();
                }catch (InterruptedException e){
                    LOGGER.error("Error attempting to get election complete electionLock");
                }
            }
            //currently an elction going on
        }else{
            //someone else has already called an election.
            LOGGER.warn("Failed attempting to get a electionLock for holding election.  Election failed.  Another node may have initiated election");
        }
    }


    //listen for winning an election
    private final void listenForElection(){
        Runnable runner = new Runnable() {
            public void run() {
                try {
                    electQueue.take();

                    //order of operations is important, don't want to get lock before updating the name
                    masterMap.put(MASTER_KEY, name);

                    //we want to wait on current master to release the lock
                    masterLock.lock();

                    //node is officially the master
                    isMaster.set(true);

                    handler.handle(CloudMasterEvent.ELECTED);

                } catch (InterruptedException e) {

                    LOGGER.error("Failed while waiting on election queue.", e);

                    //try for another election
                    elect();

                } finally {
                    electComplete.add("complete");
                    listenForElection();
                }
            }
        };
        electionPool.submit(runner);
    }

    //if a node stops responding (looses its electionLock, then hold an election)
    private final void listenForMasterLost(){
        Runnable runner = new Runnable() {
            @Override
            public void run() {
                //if there is no master, then nothing to wait for failure on
                if(masterMap.get(MASTER_KEY) == null){
                    try {
                        Thread.sleep(1000);
                    } catch (InterruptedException e) {
                        LOGGER.error("Failed sleeping while waiting for a master to be set");
                    }
                    listenForMasterLost();
                    return;
                }

                //this node caught the master failure
                masterLock.lock();

                try {
                    //possible master was told to shutdown or caught after already detected and election was called
                    if (masterMap.get(MASTER_KEY) == null) {
                        listenForMasterLost();
                        return;
                    }

                    masterMap.remove(MASTER_KEY);
                }finally {
                    masterLock.unlock();
                }

                elect();
            }
        };
        masterLostPool.submit(runner);
    }

    private void listenForStateChanged() {
        Runnable runner = new Runnable() {
            @Override
            public void run() {
                try {
                    if (isMaster.get() && masterMap != null && !name.equals(masterMap.get(MASTER_KEY))) {
                        isMaster.set(false);
                        handler.handle(CloudMasterEvent.DEMOTED);

                        //if current node is holding lock, then we just pass through this
                        //really a protection against calling unlock when we do not have the lock
                        masterLock.lock();
                        masterLock.unlock();
                    }
                    //error checking in with group, probably not a master anymore
                } catch(NullPointerException npe){
                    LOGGER.error("Failed while checking state from node " + group+":"+name+".  Cluster issue.", npe);

                    //should assume we need to be demoted
                    if(isMaster()) {
                        isMaster.set(false);
                        handler.handle(CloudMasterEvent.DEMOTED);
                    }
                }finally {
                    try {
                        Thread.sleep(1000);
                    } catch (InterruptedException e) {
                        LOGGER.error("Node " + name + " failed sleeping...", e);
                    }
                    listenForStateChanged();
                }
            }
        };
        masterChangedPool.submit(runner);
    }
}