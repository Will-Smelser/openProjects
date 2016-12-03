package com.mediocredeveloper.cloud2.master;

import com.mediocredeveloper.cloud2.message.CloudMessage;
import com.mediocredeveloper.cloud2.message.CloudMessageHandler;
import com.mediocredeveloper.cloud2.message.CloudResp;

/**
 * Created by Will2 on 12/3/2016.
 */
public class CloudMasterElectListener implements CloudMessageHandler<CloudMasterAction, CloudResp> {

    private final CloudMaster master;

    public CloudMasterElectListener(CloudMaster master){
        this.master = master;
    }

    @Override
    public CloudResp handle(CloudMessage<CloudMasterAction> message) {
        switch(message.getMessage()){
            case NO_MASTER:
                master.isMaster(false);
                return CloudResp.YES;
            default: throw new IllegalArgumentException("Unmapped CloudAction: "+message.getMessage().name());
        }
    }
}
