package com.uprisun.keycloak.util;

import org.jboss.logging.Logger;
import com.uprisun.util.HttpRequestUtil;
import org.json.JSONObject;
import java.util.function.Consumer;

public class WebhookService {
    private static final Logger logger = Logger.getLogger(WebhookService.class);

    public static void send(String url, JSONObject data) {
        Consumer<String> callback = responseBody -> {
            logger.info("Response from webhook: " + responseBody);
        };

        HttpRequestUtil.post(url, data, callback);
    }
}