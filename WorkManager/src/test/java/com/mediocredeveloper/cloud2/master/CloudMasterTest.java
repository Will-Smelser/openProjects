package com.mediocredeveloper.cloud2.master;

import com.hazelcast.config.Config;
import com.hazelcast.core.Hazelcast;
import com.hazelcast.core.HazelcastInstance;
import com.mediocredeveloper.cloud2.message.CloudMessageError;
import com.mediocredeveloper.cloud2.registry.CloudRegistry;
import org.junit.Test;

import java.util.concurrent.TimeUnit;

import static junit.framework.Assert.assertTrue;
import static junit.framework.TestCase.assertFalse;

/**
 * Created by Will2 on 12/3/2016.
 */
public class CloudMasterTest {

    public static HazelcastInstance hcast1 = Hazelcast.newHazelcastInstance(new Config());
    public static HazelcastInstance hcast2 = Hazelcast.newHazelcastInstance(new Config());


    @Test
    public void verifySingleElection() throws CloudMessageError {
        CloudRegistry registry1 = new CloudRegistry(hcast1, "group", "node1","node2");
        CloudRegistry registry2 = new CloudRegistry(hcast1, "group", "node2","node1");
        CloudMaster node1 = new CloudMaster(registry1,hcast1, new MasterEvtHandler());
        CloudMaster node2 = new CloudMaster(registry2,hcast2, new MasterEvtHandler());

        node1.elect(1, TimeUnit.SECONDS);
        assertTrue(node1.isMaster());
        assertFalse(node2.isMaster());

        node2.elect(1, TimeUnit.SECONDS);
        assertTrue(node2.isMaster());
        assertFalse(node1.isMaster());

    }

    public static class MasterEvtHandler implements CloudMasterEventHandler{
        @Override
        public void handle(CloudMasterEvent event) {

        }
    }
}
