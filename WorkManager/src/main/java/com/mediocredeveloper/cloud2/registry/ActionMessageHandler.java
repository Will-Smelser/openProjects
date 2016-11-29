package com.mediocredeveloper.cloud2.registry;

import com.mediocredeveloper.cloud2.message.CloudMessage;
import com.mediocredeveloper.cloud2.message.CloudMessageHandler;

/**
 * Created by Will2 on 11/27/2016.
 */
final class ActionMessageHandler implements CloudMessageHandler<Action> {

    private final CloudRegistry registry;

    public ActionMessageHandler(CloudRegistry registry){
        this.registry = registry;
    }

    @Override
    public Action handle(CloudMessage<Action> message) {
        switch(message.getMessage()){
            case UP: return Action.YES;
            case IS_MASTER: return registry.isMaster() ? Action.YES : Action.NO;
            case NOT_MASTER:
                registry.isMaster(false);
                return Action.YES;
            default: throw new IllegalArgumentException("Unmapped Action: "+message.getMessage().name());
        }
    }
}