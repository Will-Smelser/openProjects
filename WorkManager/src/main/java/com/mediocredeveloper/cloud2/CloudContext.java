package com.mediocredeveloper.cloud2;

import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

/**
 * Created by Will2 on 12/3/2016.
 */
public class CloudContext {
    public static volatile Map<String,Object> ctx = new ConcurrentHashMap<>();

    public static <T> T lookup(String group, String name, Class<T> clazz) {
        Object obj = ctx.get(getName(group, name, clazz));

        if(obj == null){
            return null;
        }

        return clazz.cast(obj);
    }

    public static void register(String group, String name, Class clazz, Object obj) {
        String objName = getName(group, name, clazz);
        if(lookup(group, name, clazz) != null){
            throw new IllegalStateException("Cannot re-register an object -> "+objName);
        }
        ctx.put(getName(group, name, clazz), obj);
    }

    private static String getName(String group, String name, Class type){
        return type.getCanonicalName()+ "." + group + "." + name + ".";
    }
}
