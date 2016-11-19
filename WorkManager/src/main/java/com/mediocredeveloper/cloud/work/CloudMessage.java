package com.mediocredeveloper.cloud.work;

/**
 * A wrapper for intra cloud messaging.
 */
public class CloudMessage <T> {
    private final String from;
    private final String to;
    private final T message;

    public CloudMessage(String from, String to, T message){
        this.from = from;
        this.to = to;
        this.message = message;
    }

    public String getTo(){
        return to;
    }

    public T getMessage(){
        return message;
    }

    public String getFrom(){
        return from;
    }
}
