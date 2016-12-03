package com.mediocredeveloper.cloud.work;

/**
 * A wrapper for intra cloud messaging.
 */
public class CloudMessage <T> {
    private final String from;
    private final String to;
    private final T message;
    private final String sysMessage;

    public CloudMessage(String from, String to, T message){
        this(from, to, message, null);
    }

    CloudMessage(String from, String to, T message, String sysMessage){
        this.from = from;
        this.to = to;
        this.message = message;
        this.sysMessage = null;
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

    String getSysMessage(){
        return sysMessage;
    }
}
