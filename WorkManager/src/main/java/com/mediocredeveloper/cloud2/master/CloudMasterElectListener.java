package com.mediocredeveloper.cloud2.master;

import com.mediocredeveloper.cloud2.CloudContext;
import com.mediocredeveloper.cloud2.message.CloudMessage;
import com.mediocredeveloper.cloud2.message.CloudMessageHandler;
import com.mediocredeveloper.cloud2.message.CloudResp;

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
        CloudMaster master = CloudContext.lookup(group, name, CloudMaster.class);
        switch(message.getMessage()){
            case ELECTION_COMPLETE:
                master.releaseWaitElectionComplete();
                return CloudResp.YES;
            case NO_MASTER:
                master.isMaster(false);
                return CloudResp.YES;
            case WAIT_ON_FAILURE:
                if(!master.isMaster()) {
                    master.waitForFailure();
                }
                return CloudResp.YES;
            default: throw new IllegalArgumentException("Unmapped CloudAction: "+message.getMessage().name());
        }
    }
}
