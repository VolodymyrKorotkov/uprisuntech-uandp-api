package com.uprisun.keycloak.providers.webhooks;

import com.uprisun.keycloak.util.EventSerializer;
import com.uprisun.keycloak.util.EventUtil;
import com.uprisun.keycloak.util.WebhookService;
import org.jboss.logging.Logger;
import org.keycloak.events.Event;
import org.keycloak.events.EventListenerProvider;
import org.keycloak.events.EventType;
import org.keycloak.events.admin.AdminEvent;
import org.keycloak.events.admin.OperationType;
import org.keycloak.models.KeycloakSession;
import org.keycloak.models.RealmModel;
import org.keycloak.models.UserModel;

import java.util.HashMap;
import java.util.Map;
import java.util.Set;

public class WebhooksEventListenerProvider implements EventListenerProvider {
    private KeycloakSession session;

    private Set<EventType> trackedEvents;
    private Set<OperationType> trackedOperations;

    private String eventTriggerUrl;

    private Logger logger = getLogger();

    public WebhooksEventListenerProvider(
            KeycloakSession session,
            Set<EventType> trackedEvents, Set<OperationType> trackedOperations,
            String eventTriggerUrl) {
        this.session = session;

        this.trackedEvents = trackedEvents;
        this.trackedOperations = trackedOperations;

        this.eventTriggerUrl = eventTriggerUrl;
    }

    @Override
    public void onEvent(Event event) {
        if (eventTriggerUrl == null)
            return;

        logger.trace("Event emitted: " + EventUtil.toString(event));
        String userId = event.getUserId();
        if (userId == null)
            return;

        if (trackedEvents == null || trackedEvents.contains(event.getType())) {
            Map<String, String> attributes = new HashMap<>();

            logger.debug("Tracking event: " + EventUtil.toString(event, attributes));

            WebhookService.send(eventTriggerUrl, EventSerializer.toJsonObject(event, attributes));
        }
    }

    @Override
    public void onEvent(AdminEvent adminEvent, boolean includeRepresentation) {
        if (eventTriggerUrl == null) {
            return;
        }

        logger.trace("Operation emitted: " + EventUtil.toString(adminEvent));
        if (trackedOperations == null || trackedOperations.contains(adminEvent.getOperationType())) {
            logger.debug("Tracking operation: " + EventUtil.toString(adminEvent));

            WebhookService.send(eventTriggerUrl, EventSerializer.toJsonObject(adminEvent));
        }
    }

    @Override
    public void close() {
    }

    private Logger getLogger() {
        return Logger.getLogger("com.uprisun.keycloak.providers.webhooks.WebhooksEventListenerProvider");
    }
}