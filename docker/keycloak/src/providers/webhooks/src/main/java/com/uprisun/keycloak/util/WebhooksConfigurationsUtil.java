package com.uprisun.keycloak.util;

import java.util.Arrays;
import java.util.List;
import java.util.Optional;

public class WebhooksConfigurationsUtil {
    public static Optional<String> getConfig(String key) {
        String value = System.getenv(key);
        if (value == null) {
            value = System.getProperty(key);
        }
        return Optional.ofNullable(value);
    }

    public static boolean getBooleanConfig(String key) {
        return getConfig(key).map(Boolean::parseBoolean).orElse(false);
    }

    public static String getRequiredConfig(String key) {
        return getConfig(key).orElseThrow(() -> new IllegalStateException("Configuration required for key: " + key));
    }

    public static List<String> getArrayConfig(String key, String delimiter) {
        return getConfig(key)
                .map(val -> Arrays.asList(val.split(delimiter)))
                .orElseThrow(() -> new IllegalStateException("Array configuration required for key: " + key));
    }

    public static List<String> getArrayConfig(String key) {
        return getArrayConfig(key, ",");
    }
}
