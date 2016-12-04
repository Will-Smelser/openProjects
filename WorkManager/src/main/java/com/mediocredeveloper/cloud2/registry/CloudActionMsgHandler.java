package com.mediocredeveloper.cloud2.registry;

import com.mediocredeveloper.cloud2.CloudContext;
import com.mediocredeveloper.cloud2.CloudNodeEvent;
import com.mediocredeveloper.cloud2.message.CloudMessage;
import com.mediocredeveloper.cloud2.message.CloudMessageHandler;
import com.mediocredeveloper.cloud2.message.CloudResp;

/**
 * Created by Will2 on 11/27/2016.
 */
final class CloudActionMsgHandler implements CloudMessageHandler<CloudAction, CloudResp> {

    private final String group, name;
    public CloudActionMsgHandler(String group, String name){
        this.group = group;
        this.name = name;
    }

    @Override
    public CloudResp handle(CloudMessage<CloudAction> message) {
        CloudRegistry registry = CloudContext.lookup(group, name, CloudRegistry.class);
        switch(message.getMessage()){
            case UP: return CloudResp.YES;
            case ADD:
                registry.add();
                registry.notify(CloudNodeEvent.ADD);
                return CloudResp.YES;
            case REMOVE:
                registry.remove();
                registry.notify(CloudNodeEvent.REMOVE);
                return CloudResp.YES;
            default: throw new IllegalArgumentException("Unmapped CloudAction: "+message.getMessage().name());
        }
    }
}