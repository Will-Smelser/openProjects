package com.mediocredeveloper.cloud2.master;

import com.mediocredeveloper.cloud2.CloudContext;
import com.mediocredeveloper.cloud2.message.CloudMessage;
import com.mediocredeveloper.cloud2.message.CloudMessageHandler;
import com.mediocredeveloper.cloud2.message.CloudResp;

import javax.naming.NamingException;

/**
 * Created by Will2 on 12/3/2016.
 */
public class CloudMasterElectListener implements CloudMessageHandler<CloudMasterAction, CloudResp> {
    private final String group, name;

    public CloudMasterElectListener(String group, String name){
        this.group = group;
        this.name = name;
    }

    @Override
    public CloudResp handle(CloudMessage<CloudMasterAction> message) {
        System.err.println("I got the message!!!"+message.getMessage());
        switch(message.getMessage()){
            case NO_MASTER:
                CloudMaster master = CloudContext.lookup(group, name, CloudMaster.class);
                master.isMaster(false);
                return CloudResp.YES;
            default: throw new IllegalArgumentException("Unmapped CloudAction: "+message.getMessage().name());
        }
    }
}
