package com.uprisun.keycloak.util;

import org.json.JSONObject;
import org.keycloak.events.Event;
import org.keycloak.events.admin.AdminEvent;

import java.util.Map;

public class EventSerializer {
    public static JSONObject toJsonObject(Event event, Map<String, String> attributes) {
        JSONObject result = new JSONObject();

        result.put("type", event.getType().name());
        result.put("realmId", event.getRealmId());
        result.put("clientId", event.getClientId());
        result.put("userId", event.getUserId());

        if (event.getDetails() != null) {
            result.put("details", event.getDetails());
        }

        if (event.getError() != null) {
            result.put("error", event.getError());
        }

        if (attributes != null) {
            result.put("attributes", attributes);
        }

        result.put("time", event.getTime());

        return result;
    }

    public static JSONObject toJsonObject(AdminEvent adminEvent) {
        JSONObject result = new JSONObject();

        result.put("type", adminEvent.getOperationType().name());
        result.put("realmId", adminEvent.getRealmId());
        result.put("clientId", adminEvent.getAuthDetails().getClientId());
        result.put("userId", adminEvent.getAuthDetails().getUserId());

        result.put("resourceType", adminEvent.getResourceType().name());
        result.put("resourcePath", adminEvent.getResourcePath());

        if (adminEvent.getError() != null) {
            result.put("error", adminEvent.getError());
        }

        result.put("time", adminEvent.getTime());

        return result;
    }
}
