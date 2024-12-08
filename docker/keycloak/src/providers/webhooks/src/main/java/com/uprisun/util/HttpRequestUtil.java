package com.uprisun.util;

import org.json.JSONObject;

import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.net.http.HttpRequest.BodyPublishers;
import java.net.http.HttpResponse.BodyHandlers;
import java.util.concurrent.CompletableFuture;
import java.util.function.Consumer;

public class HttpRequestUtil {
        private static final HttpClient httpClient = HttpClient.newBuilder()
                        .version(HttpClient.Version.HTTP_2)
                        .build();

        public static CompletableFuture<Void> post(String url, JSONObject body, Consumer<String> callback) {
                String requestBody = body.toString();

                HttpRequest request = HttpRequest.newBuilder()
                                .uri(URI.create(url))
                                .header("Content-Type", "application/json")
                                .POST(BodyPublishers.ofString(requestBody))
                                .build();

                return httpClient.sendAsync(request, BodyHandlers.ofString())
                                .thenApply(HttpResponse::body)
                                .thenAccept(responseBody -> {
                                        System.out.println(responseBody);
                                        callback.accept(responseBody);
                                });
        }
}