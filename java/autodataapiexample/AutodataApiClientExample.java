package autodataapiexample;

import java.util.LinkedHashMap;
import java.util.Map;

import org.json.JSONObject;

/**
 * This class is used as a client to invoke different methods in the API.
 *
 * @company Autodata Limited
 */
public class AutodataApiClientExample {

    public static void main(String[] args) {
        // You can change ApiClient here
        ApiClient client = ApiClientOauth.getInstance();

        // V1 API examples
        manuafacturersExample(client);
        serviceSchedulesExample(client);
        serviceScheduleExample(client);
        errorExample(client);
        errorPostExample(client);

        // V1 Subscription API examples
        usersListExample(client);
    }

    /**
     * Retrieve manufacturers by country:: v1/manufacturers
     */
    private static void manuafacturersExample(ApiClient client) {
        Map<String, String> endpoint = new LinkedHashMap<>();
        endpoint.put("v1", null);
        endpoint.put("manufacturers", null);

        System.out.println("Example 1. Retrieve a list of manufacturers");
        try {
            System.out.println(client.call(endpoint, "GET"));
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
    }

    /**
     * Search for service schedules by MID:: v1/vehicles/:mid/service-schedules
     */
    private static void serviceSchedulesExample(ApiClient client) {
        Map<String, String> endpoint = new LinkedHashMap<>();
        endpoint.put("v1", null);
        endpoint.put("vehicles", "AUD00528");
        endpoint.put("service-schedules", null);

        System.out.println("Example 2. Retrieve a list of Service Schedules");
        try {
            System.out.println(client.call(endpoint, "GET"));
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
    }

    /**
     * Retrieve service schedule data:: v1/vehicles/:mid/service-schedules/:service_schedule_id
     */
    private static void serviceScheduleExample(ApiClient client) {
        System.out.println("Example 3. Retrieve a specific Service Schedule");
        try {
            // Get first dervice schedule id from last response
            JSONObject lastResponse = new JSONObject(client.getLastResponse());
            JSONObject serviceSchedule = lastResponse.getJSONArray("data").getJSONObject(0);
            String serviceScheduleId = serviceSchedule.getString("service_schedule_id");

            Map<String, String> endpoint = new LinkedHashMap<>();
            endpoint.put("v1", null);
            endpoint.put("vehicles", "AUD00528");
            endpoint.put("service-schedules", serviceScheduleId);

            System.out.println(client.call(endpoint, "GET"));
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
    }

    /**
     * No required data provided example
     */
    private static void errorExample(ApiClient client) {
        Map<String, String> endpoint = new LinkedHashMap<>();
        endpoint.put("v1", null);
        endpoint.put("vehicles", null);
        endpoint.put("service-schedules", null);

        System.out.println("Example 4. No required data provided example");
        try {
            System.out.println(client.call(endpoint, "GET"));
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
    }

    /**
     * Invalid request type example
     */
    private static void errorPostExample(ApiClient client) {
        Map<String, String> endpoint = new LinkedHashMap<>();
        endpoint.put("v1", null);
        endpoint.put("vehicles", null);
        endpoint.put("service-schedules", null);

        System.out.println("Example 5. Invalid request type example");
        try {
            System.out.println(client.call(endpoint, "POST"));
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
    }

    /**
     * Get user list:: /accounts/v1/users
     */
    private static void usersListExample(ApiClient client) {
        Map<String, String> endpoint = new LinkedHashMap<>();
        endpoint.put("accounts", null);
        endpoint.put("v1", null);
        endpoint.put("users", null);

        System.out.println("Example 6. Retrieve users list for account");
        try {
            System.out.println(client.call(endpoint, "GET"));
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
    }

}
