package com.uprisun.keycloak.util;

import org.keycloak.events.Event;
import org.keycloak.events.admin.AdminEvent;

import java.util.Map;

public class EventUtil {
    public static String toString(Event event) {
        return toString(event, null);
    }

    public static String toString(Event event, Map<String, String> attributes) {
        StringBuilder sb = new StringBuilder();

        sb.append("type=");
        sb.append(event.getType());
        sb.append(", realmId=");
        sb.append(event.getRealmId());
        sb.append(", clientId=");
        sb.append(event.getClientId());
        sb.append(", userId=");
        sb.append(event.getUserId());
        sb.append(", ipAddress=");
        sb.append(event.getIpAddress());

        if (event.getError() != null) {
            sb.append(", error=");
            sb.append(event.getError());
        }

        if (event.getDetails() != null) {
            sb.append(", details={");
            sb.append(toString(event.getDetails()));
            sb.append("}");
        }

        if (attributes != null) {
            sb.append(", attributes={");
            sb.append(toString(attributes));
            sb.append("}");
        }

        sb.append(", time=");
        sb.append(event.getTime());

        return sb.toString();
    }

    public static String toString(AdminEvent adminEvent) {
        StringBuilder sb = new StringBuilder();

        sb.append("operationType=");
        sb.append(adminEvent.getOperationType());
        sb.append(", realmId=");
        sb.append(adminEvent.getAuthDetails().getRealmId());
        sb.append(", clientId=");
        sb.append(adminEvent.getAuthDetails().getClientId());
        sb.append(", userId=");
        sb.append(adminEvent.getAuthDetails().getUserId());
        sb.append(", ipAddress=");
        sb.append(adminEvent.getAuthDetails().getIpAddress());
        sb.append(", resourcePath=");
        sb.append(adminEvent.getResourcePath());

        if (adminEvent.getError() != null) {
            sb.append(", error=");
            sb.append(adminEvent.getError());
        }

        sb.append(", time=");
        sb.append(adminEvent.getTime());

        return sb.toString();
    }

    private static String toString(Map<String, String> map) {
        StringBuilder sb = new StringBuilder();

        for (Map.Entry<String, String> entry : map.entrySet()) {
            if (sb.length() > 0) {
                sb.append(", ");
            }

            sb.append(entry.getKey());

            if (entry.getValue() == null || entry.getValue().indexOf(' ') == -1) {
                sb.append("=");
                sb.append(entry.getValue());
            } else {
                sb.append("='");
                sb.append(entry.getValue());
                sb.append("'");
            }
        }

        return sb.toString();
    }
}
