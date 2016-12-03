package com.mediocredeveloper.cloud2.registry;

import com.mediocredeveloper.cloud2.message.CloudMessage;
import com.mediocredeveloper.cloud2.message.CloudMessageHandler;
import com.mediocredeveloper.cloud2.message.CloudResp;

/**
 * Created by Will2 on 11/27/2016.
 */
final class CloudActionMsgHandler implements CloudMessageHandler<CloudAction, CloudResp> {

    @Override
    public CloudResp handle(CloudMessage<CloudAction> message) {
        switch(message.getMessage()){
            case UP: return CloudResp.YES;
            case REMOVE:
                return CloudResp.YES;
            default: throw new IllegalArgumentException("Unmapped CloudAction: "+message.getMessage().name());
        }
    }
}