package com.mediocredeveloper.cloud.work;

import com.hazelcast.config.Config;
import com.hazelcast.core.Hazelcast;
import com.hazelcast.core.HazelcastInstance;
import org.junit.Test;

import java.util.List;
import java.util.concurrent.CopyOnWriteArrayList;

import static junit.framework.Assert.assertEquals;
import static junit.framework.TestCase.assertTrue;

/**
 * Created by Will2 on 11/19/2016.
 */
public class CloudTest {
    @Test
    public void verifyQueueWorks() throws InterruptedException {

        HazelcastInstance hcast1 = Hazelcast.newHazelcastInstance(new Config());
        HazelcastInstance hcast2 = Hazelcast.newHazelcastInstance(new Config());

        final List<String> work = new CopyOnWriteArrayList<>();

        final String group = "group";
        final String host1 = "host1";
        final String host2 = "host2";

        Cloud<Object> cloud1 = new Cloud<>(group, host1, hcast1, new CloudWorker<Object>(){
            @Override
            public void doWork(Object obj) {
                work.add(host1);
            }
        });

        Cloud<Object> cloud2 = new Cloud<>(group, host2, hcast2, new CloudWorker<Object>(){
            @Override
            public void doWork(Object obj) {
                work.add(host2);
            }
        });

        //make sure someone claims being master
        //assertTrue((cloud1.isMaster() && !cloud2.isMaster())||(!cloud1.isMaster() && cloud2.isMaster()));

        cloud1.post(work);
        cloud2.post(work);

        int counter = 2; //the first 2 post

        while(counter < 200){
            cloud1.post(work);
            cloud2.post(work);
            counter = counter + 2;
        }

        //make sure threads have time to consume all work
        //maybe need to have a shutdown method, instead of hack like this
        Thread.sleep(100);

        assertEquals(counter, work.size());

    }
}
