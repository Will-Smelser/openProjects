package com.mediocredeveloper.cloud2.registry;

import com.hazelcast.core.HazelcastInstance;
import com.hazelcast.core.IMap;
import com.mediocredeveloper.cloud2.message.CloudMessageError;
import com.mediocredeveloper.cloud2.message.CloudMessageHandler;
import com.mediocredeveloper.cloud2.message.CloudMessageServicer;
import com.mediocredeveloper.cloud2.message.CloudResp;
import com.mediocredeveloper.cloud2.util.CompletedFuture;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.*;
import java.util.concurrent.*;
import java.util.concurrent.atomic.AtomicBoolean;

/**
 * Created by Will2 on 11/27/2016.
 */
public class CloudRegistry {
    private static final Logger LOGGER = LoggerFactory.getLogger(CloudRegistry.class);

    private static final String REGISTRY = "%s_REGISTRY";

    private static final long UPDATE_INTERVAL = 5000;

    private final HazelcastInstance hcast;

    private final IMap<String, Long> registry;

    private final CloudActionMsgHandler handler;

    private final Map<String, CloudMessageServicer<CloudAction, CloudResp>> msgSrvcMap = new ConcurrentHashMap<>();

    private final String group;

    private final String name;

    private final Timer timer;

    private final AtomicBoolean isMaster = new AtomicBoolean(false);

    private final ExecutorService electionPool =  Executors.newSingleThreadExecutor();

    /**
     * Create a registry of nodes.
     * @param hcast
     * @param group The group name these nodes are part of.
     * @param name This nodes unique name.
     * @param names The other nodes names.
     */
    public CloudRegistry(final HazelcastInstance hcast, final String group, final String name, final String... names){
        this.group = group;
        this.name = name;
        this.handler = new CloudActionMsgHandler();
        this.registry = hcast.getMap(String.format(REGISTRY, group));
        this.hcast = hcast;

        //build registry and the msg service
        for(String n : names)  add(n);

        //make sure this node has its updated timestamp
        registry.put(name, System.currentTimeMillis());

        //just periodically update, if it is no longer in registry map, the timer will kill itself upon waking up.
        TimerTask timerTask = new RegularUpdate(this);
        this.timer = new Timer();
        this.timer.scheduleAtFixedRate(timerTask, UPDATE_INTERVAL, UPDATE_INTERVAL);
    }

    /**
     * Get this elements name.
     * @return
     */
    public String getName(){
        return name;
    }

    public String getGroup(){
        return group;
    }

    /**
     * Chec if the node is up UP.  The future returned should hold the response.
     * @param name The unique node to send message to.
     * @return
     */
    public Future<CloudResp> check(String name){
        if(!registry.containsKey(name)){
            throw new IllegalArgumentException("Node "+name+" is not registered.");
        }
        return send(name, CloudAction.UP);
    }

    /**
     * Iterate the registry verifying everyone is up, atleast within the time provided.
     * @param timeout
     * @param unit
     * @return
     */
    public boolean check(long timeout, TimeUnit unit){
        for(Map.Entry<String, CloudMessageServicer<CloudAction, CloudResp>> entry : msgSrvcMap.entrySet()){
            String name = entry.getKey();
            try {
                CloudResp result = entry.getValue().send(entry.getKey(), CloudAction.UP, timeout, unit);
                if(result != CloudResp.YES){
                    return false;
                }
                registry.put(name, System.currentTimeMillis());
            } catch (TimeoutException e) {
                LOGGER.error("Timeout on testing node is up: "+group+":"+name, e);
                return false;
            } catch (Exception e) {
                LOGGER.error("Error on testing node is up: "+group+":"+name, e);
                return false;
            }
        }
        return true;
    }

    /**
     * Remove a registry entry.
     * @param name
     */
    public synchronized Future<CloudResp> remove(String name) {
        Future<CloudResp> result = send(name, CloudAction.REMOVE);
        registry.remove(name);
        msgSrvcMap.remove(name);

        return result;
    }

    /**
     * Add a node to registry. Does not wait to check if the node is actually up.  Just fires a message.
     * @param name The unique name to add.  This does not check if it exists already.  So this will overwrite it.
     */
    public synchronized Future<CloudResp> add(String name){
        registry.put(name, 0L);
        msgSrvcMap.put(name, new CloudMessageServicer<>(group, name, hcast, handler));

        return send(name, CloudAction.ADD);
    }

    /**
     * Check the last known time this node updated its time stamp
     * @param name
     * @return
     */
    public Future<CloudResp> up(String name) throws CloudMessageError {
        if(!registry.containsKey(name)){
            throw new IllegalStateException("Node "+name+" is not registered.");
        }
        boolean quickTest = System.currentTimeMillis() - registry.get(name) > UPDATE_INTERVAL*1.10;
        if(!quickTest){
            CloudMessageServicer<CloudAction, CloudResp> service = msgSrvcMap.get(name);
            return service.send(name, CloudAction.UP);
        }

        return new CompletedFuture(quickTest? CloudResp.YES : CloudResp.NO);
    }

    /**
     * Get all the registered nodes.
     * @return
     */
    public Set<String> getRegistered(){
        return this.msgSrvcMap.keySet();
    }

    /**
     * send a action message.  If it fails, then the error is trapped, but will return null.
     * @param name
     * @param action
     * @return
     */
    private Future<CloudResp> send(String name, CloudAction action){
        CloudMessageServicer<CloudAction, CloudResp> service = msgSrvcMap.get(name);
        try {
            return service.send(name, action);
        } catch (CloudMessageError e) {
            LOGGER.error("Error sending message - " + group + ":" + name + "->" + action.name());
            throw new IllegalStateException("Failed sending message.", e);
        }
    }

    /**
     * This is a timer task which just periodically will update the registry with when it ran.  This just gives us a quick check to verify
     * that a node is up.
     */
    private static class RegularUpdate extends TimerTask {
        private final CloudRegistry registry;
        public RegularUpdate(CloudRegistry registry){
            this.registry = registry;
        }
        @Override
        public void run() {
            if(!registry.registry.containsKey(registry.name)){
                registry.timer.cancel();
                return;
            }
            registry.registry.put(registry.name, System.currentTimeMillis());
        }
    }
}
