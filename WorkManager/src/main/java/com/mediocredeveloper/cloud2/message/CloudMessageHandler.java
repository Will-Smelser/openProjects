package com.mediocredeveloper.cloud2.message;

import java.io.Serializable;

/**
 * Created by Will2 on 11/25/2016.
 */
public interface CloudMessageHandler <T extends Serializable> extends Serializable {
    T handle(CloudMessage<T> message);
}
