package com.mediocredeveloper.cloud.work;

/**
 * A wrapper for intra cloud messaging.
 */
public class CloudMessage <T> {
    private final String from;
    private final T message;
    public CloudMessage(String from, T message){
        this.from = from;
        this.message = message;
    }
}
