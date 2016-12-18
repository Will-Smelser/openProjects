package com.mediocredeveloper.cloud2.master;

import com.hazelcast.config.Config;
import com.hazelcast.core.Hazelcast;
import com.hazelcast.core.HazelcastInstance;
import com.mediocredeveloper.cloud2.CloudNodeEvent;
import com.mediocredeveloper.cloud2.CloudNodeEventListener;
import com.mediocredeveloper.cloud2.message.CloudMessageError;
import com.mediocredeveloper.cloud2.registry.CloudRegistry;
import org.junit.Test;

import javax.naming.NamingException;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.TimeUnit;

import static junit.framework.Assert.assertTrue;
import static junit.framework.TestCase.assertFalse;

/**
 * Created by Will2 on 12/3/2016.
 */
public class CloudMasterTest {

    public static HazelcastInstance hcast1 = Hazelcast.newHazelcastInstance(new Config());
    public static HazelcastInstance hcast2 = Hazelcast.newHazelcastInstance(new Config());
    public static HazelcastInstance hcast3 = Hazelcast.newHazelcastInstance(new Config());
    public static HazelcastInstance hcast4 = Hazelcast.newHazelcastInstance(new Config());
    public static HazelcastInstance hcast5 = Hazelcast.newHazelcastInstance(new Config());

    @Test
    public void verifyFailurePickedUp() throws CloudMessageError, ExecutionException, InterruptedException {
        CloudRegistry registry1 = new CloudRegistry(hcast3, new NodeEvtHandler(), "test1", "node1");
        CloudRegistry registry2 = new CloudRegistry(hcast4, new NodeEvtHandler(), "test1", "node2");
        CloudRegistry registry4 = new CloudRegistry(hcast5, new NodeEvtHandler(), "test1", "node4");

        CloudMaster node1 = new CloudMaster(registry1, hcast3, new MasterEvtHandler("test1","node1"));
        CloudMaster node2 = new CloudMaster(registry2, hcast4, new MasterEvtHandler("test1","node2"));
        CloudMaster node4 = new CloudMaster(registry4, hcast5, new MasterEvtHandler("test1","node4"));

        node1.elect(5, TimeUnit.SECONDS);

        CloudMaster nextMaster = node1.isMaster() ? node2 : node1;
        CloudMaster currentMaster = node1.isMaster() ? node1 : node2;
        HazelcastInstance master = node1.isMaster() ? hcast3 : hcast4;
        HazelcastInstance nextMasterHcast = node1.isMaster() ? hcast4 : hcast3;

        assertTrue(currentMaster.isMaster());

        //kill the current master
        master.shutdown();

        nextMaster.waitIfElection();

        assertTrue(nextMaster.isMaster());

        System.out.println("\n>>>>CHECKING NODE 4->" + registry4.up("node4").get().name());

        //nextMasterHcast.shutdown();
        node4.elect(4, TimeUnit.SECONDS);

        //node4.waitForFailure();
        node4.waitIfElection();

        assertTrue(node4.isMaster());

    }

    @Test
    public void verifySingleElection() throws CloudMessageError, InterruptedException {
        CloudRegistry registry1 = new CloudRegistry(hcast1, new NodeEvtHandler(), "test2", "node1");
        CloudRegistry registry2 = new CloudRegistry(hcast2,  new NodeEvtHandler(), "test2", "node2");
        CloudMaster node1 = new CloudMaster(registry1, hcast1, new MasterEvtHandler());
        CloudMaster node2 = new CloudMaster(registry2, hcast2, new MasterEvtHandler());

        node1.elect(30, TimeUnit.SECONDS);


        if(node1.isMaster()){
            assertFalse(node2.isMaster());
        }else{
            assertTrue(node2.isMaster());
        }


        node2.elect(30, TimeUnit.SECONDS);

        if(node1.isMaster()){
            assertFalse(node2.isMaster());
        }else{
            assertTrue(node2.isMaster());
        }


    }

    @Test
    public void verifyABunchOfElections() throws InterruptedException {

        CloudRegistry registry1 = new CloudRegistry(hcast1, new NodeEvtHandler(), "test3", "node3");
        CloudRegistry registry2 = new CloudRegistry(hcast2, new NodeEvtHandler(), "test3", "node4");
        final CloudMaster node1 = new CloudMaster(registry1, hcast1, new MasterEvtHandler());
        final CloudMaster node2 = new CloudMaster(registry2, hcast2, new MasterEvtHandler());

        Thread t1 = new Thread(new Runnable() {
            @Override
            public void run() {
                for(int i=0; i < 500; i++){
                    try {
                        node1.elect(5, TimeUnit.SECONDS);
                    } catch (CloudMessageError cloudMessageError) {
                        cloudMessageError.printStackTrace();
                    }

                    if(node1.isMaster()){
                        assertFalse(node2.isMaster());
                    }else{
                        assertTrue(node2.isMaster());
                    }

                    try {
                        node2.elect(5, TimeUnit.SECONDS);
                    } catch (CloudMessageError cloudMessageError) {
                        cloudMessageError.printStackTrace();
                    }

                    if(node1.isMaster()){
                        assertFalse(node2.isMaster());
                    }else{
                        assertTrue(node2.isMaster());
                    };
                }
            }
        });

        Thread t2 = new Thread(new Runnable() {
            @Override
            public void run() {
                for(int i=0; i < 500; i++){
                    try {
                        node1.elect(5, TimeUnit.SECONDS);
                    } catch (CloudMessageError cloudMessageError) {
                        cloudMessageError.printStackTrace();
                    }

                    if(node1.isMaster()){
                        assertFalse(node2.isMaster());
                    }else{
                        assertTrue(node2.isMaster());
                    }

                    try {
                        node2.elect(5, TimeUnit.SECONDS);
                    } catch (CloudMessageError cloudMessageError) {
                        cloudMessageError.printStackTrace();
                    }

                    if(node1.isMaster()){
                        assertFalse(node2.isMaster());
                    }else{
                        assertTrue(node2.isMaster());
                    }
                }
            }
        });

        t1.start();
        t2.start();

        t1.join();
        t2.join();

    }

    public static class MasterEvtHandler implements CloudMasterEventHandler{
        String group = "??";
        String name = "??";
        public MasterEvtHandler(String group, String name){
            this.group = group;
            this.name = name;
        }
        public MasterEvtHandler(){}
        @Override
        public void handle(CloudMasterEvent event) {
            System.out.println("Master Evt Handler-->"+group+":"+name+"-->"+event.name());
        }
    }

    public static class NodeEvtHandler implements CloudNodeEventListener{
        @Override
        public void handle(String group, String name, CloudNodeEvent event) {
            System.out.println("Node Evt Handler-->"+group+":"+name+"-->"+event.name());
        }
    }


}
