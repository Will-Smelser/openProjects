package com.mediocredeveloper.cloud2.registry;

import com.hazelcast.core.HazelcastInstance;
import com.hazelcast.core.IMap;
import com.mediocredeveloper.cloud2.message.CloudMessage;
import com.mediocredeveloper.cloud2.message.CloudMessageError;
import com.mediocredeveloper.cloud2.message.CloudMessageHandler;
import com.mediocredeveloper.cloud2.message.CloudMessageServicer;
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

    private final HazelcastInstance hcast;

    private final IMap<String, Long> registry;

    private final CloudMessageHandler<Action> handler;

    private final Map<String, CloudMessageServicer<Action>> msgSrvcMap = new ConcurrentHashMap<>();

    private final String group;

    private final String name;

    private final Timer timer;

    private final AtomicBoolean isMaster = new AtomicBoolean(false);

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
        this.handler = new ActionMessageHandler(this);
        this.registry = hcast.getMap(String.format(REGISTRY, group));
        this.hcast = hcast;

        //build registry and the msg service
        for(String n : names)  add(n);

        //make sure this node has its updated timestamp
        registry.put(name, System.currentTimeMillis());

        //just periodically update, if it is no longer in registry map, the timer will kill itself upon waking up.
        TimerTask timerTask = new RegularUpdate(this);
        this.timer = new Timer();
        this.timer.scheduleAtFixedRate(timerTask, 5000, 5000);
    }

    /**
     * Chec if the node is up UP.  The future returned should hold the response.
     * @param name The unique node to send message to.
     * @return
     */
    public Future<Action> check(String name){
        if(!registry.containsKey(name)){
            throw new IllegalArgumentException("Node "+name+" is not registered.");
        }
        return send(name, Action.UP);
    }

    /**
     * Iterate the registry verifying everyone is up, atleast within the time provided.
     * @param timeout
     * @param unit
     * @return
     */
    public boolean check(long timeout, TimeUnit unit){
        for(Map.Entry<String, CloudMessageServicer<Action>> entry : msgSrvcMap.entrySet()){
            String name = entry.getKey();
            try {
                Action result = entry.getValue().send(entry.getKey(), Action.UP, timeout, unit);
                if(result != Action.YES){
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
    public synchronized Future<Action> remove(String name) {
        Future<Action> result = send(name, Action.REMOVE);
        registry.remove(name);
        msgSrvcMap.remove(name);

        return result;
    }

    /**
     * Add a node to registry. Does not wait to check if the node is actually up.  Just fires a message.
     * @param name The unique name to add.  This does not check if it exists already.  So this will overwrite it.
     */
    public synchronized Future<Action> add(String name){
        Future<Action> result = send(name, Action.ADD);
        registry.put(name, 0L);
        msgSrvcMap.put(name, new CloudMessageServicer<Action>(group, name, hcast, handler));

        return result;
    }

    /**
     * Check the last known time this node updated its time stamp
     * @param name
     * @return
     */
    public long lastUpdated(String name){
        if(!registry.containsKey(name)){
            throw new IllegalStateException("Node "+name+" is not registered.");
        }
        return registry.get(name);
    }

    /**
     * Elect this node as a master node.  First telling all other nodes they are not to be master node.
     * @throws CloudMessageError
     */
    public void electMaster() throws CloudMessageError {
        //tell everyone that there is no master
        List<Future<Action>> futures = new ArrayList<>();

        for(Map.Entry<String, CloudMessageServicer<Action>> entry : msgSrvcMap.entrySet()){
            CloudMessageServicer<Action> svc = entry.getValue();
            Future<Action> result = svc.send(entry.getKey(), Action.NOT_MASTER);
            if(result != null){
                futures.add(result);
            }
        }

        try {
            waitOnFutures(futures);
        } catch (InterruptedException | ExecutionException e) {
            throw new CloudMessageError("Failed waiting on all recipients to respond to removing master.", e);
        }

        this.isMaster.set(true);
    }

    //wait on a list of futures to complete
    private void waitOnFutures(Collection<Future<Action>> futures) throws ExecutionException, InterruptedException {
        for(Future<Action> msg : futures)  msg.get();
    }

    /**
     * If this node is a master
     * @return
     */
    public boolean isMaster() {
        return isMaster.get();
    }

    /**
     * User by handler to set the master node.
     * @param isMaster
     */
    void isMaster(boolean isMaster){
        this.isMaster.set(isMaster);
    }

    /**
     * send a action message.  If it fails, then the error is trapped, but will return null.
     * @param name
     * @param action
     * @return
     */
    private Future<Action> send(String name, Action action){
        CloudMessageServicer<Action> service = msgSrvcMap.get(name);
        try {
            return service.send(name, action);
        } catch (CloudMessageError e) {
            LOGGER.error("Error sending message - "+group+":"+name+"->"+action.name());
        }
        return null;
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
