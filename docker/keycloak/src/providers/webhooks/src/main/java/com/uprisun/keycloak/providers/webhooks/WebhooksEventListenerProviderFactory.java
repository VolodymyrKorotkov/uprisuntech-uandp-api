package com.uprisun.keycloak.providers.webhooks;

import com.uprisun.keycloak.util.WebhooksConfigurationsUtil;
import com.uprisun.keycloak.util.WebhooksConfigurations;
import org.jboss.logging.Logger;
import org.keycloak.Config;
import org.keycloak.events.EventListenerProvider;
import org.keycloak.events.EventListenerProviderFactory;
import org.keycloak.events.EventType;
import org.keycloak.events.admin.OperationType;
import org.keycloak.models.KeycloakSession;
import org.keycloak.models.KeycloakSessionFactory;

import java.util.HashSet;
import java.util.Set;
import java.util.List;

public class WebhooksEventListenerProviderFactory implements EventListenerProviderFactory {
    private Set<EventType> trackedEvents;
    private Set<OperationType> trackedOperations;

    private String eventTriggerUrl;

    private Logger logger = getLogger();

    @Override
    public EventListenerProvider create(KeycloakSession session) {
        return new WebhooksEventListenerProvider(
                session,
                trackedEvents, trackedOperations,
                eventTriggerUrl);
    }

    @Override
    public void init(Config.Scope config) {
        Boolean enableProvider = WebhooksConfigurationsUtil
                .getBooleanConfig(WebhooksConfigurations.ENABLE_WEBHOOKS);

        if (!enableProvider) {
            logger.info("Webhooks provider disabled");
            return;
        }

        logger.info("Initialize webhooks provider");

        List<String> configTrackedEvents = WebhooksConfigurationsUtil
                .getArrayConfig(WebhooksConfigurations.TRACKED_EVENTS);

        trackedEvents = new HashSet<>();
        for (String event : configTrackedEvents) {
            try {
                trackedEvents.add(EventType.valueOf(event));
                logger.info("Added event tracking: " + event);
            } catch (IllegalArgumentException e) {
                logger.error("Invalid event type configured: " + event);
            }
        }

        List<String> configTrackedOperations = WebhooksConfigurationsUtil
                .getArrayConfig(WebhooksConfigurations.TRACKED_EVENTS);

        if (configTrackedOperations != null) {
            trackedOperations = new HashSet<>();
            for (String operation : configTrackedOperations) {
                try {
                    trackedOperations.add(OperationType.valueOf(operation));
                    logger.info("Added operation tracking: " + operation);
                } catch (IllegalArgumentException e) {
                    logger.error("Invalid operation type configured: " + operation);
                }
            }
        }

        String configEventTriggerUrl = WebhooksConfigurationsUtil
                .getRequiredConfig(WebhooksConfigurations.EVENTS_WEBHOOK_URL);

        eventTriggerUrl = configEventTriggerUrl;
        if (eventTriggerUrl != null)
            logger.info("Event trigger url set: " + eventTriggerUrl);
    }

    @Override
    public void postInit(KeycloakSessionFactory factory) {
    }

    @Override
    public void close() {
        // RequestUtil.shutdown();
    }

    @Override
    public String getId() {
        return "webhooks";
    }

    private Logger getLogger() {
        return Logger.getLogger("com.uprisun.keycloak.providers.webhooks.WebhooksEventListenerProviderFactory");
    }
}
