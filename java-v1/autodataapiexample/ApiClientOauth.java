package autodataapiexample;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.Date;
import java.util.LinkedHashMap;
import java.util.Map;

import org.json.JSONObject;

/**
 * This code uses JSON library to convert json string representation into java objects.
 * See http://www.JSON.org/
 */

/**
 * Oauth2 authorization class for getting data from Autodata API.
 * It implements Singleton pattern and you can use it apart from ApiClient interface.
 * This code is only for demonstration purposes.
 * @company Autodata Limited
 */
public class ApiClientOauth implements ApiClient {
    
    private static ApiClientOauth instance = null;

    // API base url
    private final String baseUrl = "http://api.autodata-group.com/";
    
    // API url for user authentication
    private final String tokenUrl = "https://account.autodata-group.com/oauth/access_token";

    // API client Id
    private final String clientId = ""; // TODO: Set Client Id here
    
    // API client Secret code
    private final String clientSecret = ""; // TODO: Set Client secret here

    // default language settings
    private String countryCode = "gb";
    private String languageCode = "en-gb";

    // Oauth token parameters
    private String accessToken = "";
    private long tokenExpires = 0;
    
    private String lastResponse = "";
    
    private ApiClientOauth() {
    }
    
    public static ApiClientOauth getInstance() {
        if(instance == null) {
         instance = new ApiClientOauth();
      }
      return instance;
    }

    /**
     * Gets current country code
     * @return 2 letters based country code
     */
    @Override
    public String getCountryCode() {
        return countryCode;
    }

    /**
     * Sets current country code
     * @param countryCode
     */
    @Override
    public void setCountryCode(String countryCode) {
        this.countryCode = countryCode;
    }

    /**
     * Gets current language code
     * @return 4 letters based language code
     */
    @Override
    public String getLanguageCode() {
        return languageCode;
    }

    /**
     * Sets current language code
     * @param languageCode
     */
    @Override
    public void setLanguageCode(String languageCode) {
        this.languageCode = languageCode;
    }
    
    /**
     * Holds API response from last executed query
     * @return last API response
     */
    @Override
    public String getLastResponse() {
        return lastResponse;
    }

    /**
     * Returns secret token generated for the user
     */
    private void getToken() {
        Map<String, String> params = new LinkedHashMap<>();
        params.put("client_id", this.clientId);
        params.put("client_secret", this.clientSecret);
        params.put("grant_type", "client_credentials");
        params.put("scope", "scope1");
        params.put("state", "123456789");

        try {
            URL url = new URL(this.tokenUrl + "?" + this.mapToParams(params));

            HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            connection.setRequestMethod("POST");
            connection.setDoOutput(true);

            InputStream in = new BufferedInputStream(connection.getInputStream());
            JSONObject response = new JSONObject(convertStreamToString(in));

            this.accessToken = response.getString("access_token");
            this.tokenExpires = response.getInt("expires");
        } catch (IOException ex) {
            throw new RuntimeException(ex.getMessage());
        }
    }

    /**
     * Calls API with given parameters
     * @param endpoint map contains endpoint configuration
     * @param methodType type of query - "GET", "POST", "PUT", "DELETE"
     * @return json string that contains API response data
     */
    @Override
    public String call(Map<String, String> endpoint, String methodType) {
        return this.call(endpoint, new LinkedHashMap<String, String>(), methodType);
    }

    /**
     * Calls API with given parameters
     * @param endpoint map contains endpoint configuration
     * @param params additional parameters added to the query
     * @param methodType type of query - "GET", "POST", "PUT"
     * @return
     */
    @Override
    public String call(Map<String, String> endpoint, Map<String, String> params, String methodType) {

        int currentTime = (int) (new Date().getTime() / 1000);
        if (currentTime > this.tokenExpires) {
            this.getToken();
        }

        String requiredParams = "country-code=" + this.countryCode;
        requiredParams += "&language=" + this.languageCode;
        requiredParams += "&access_token=" + this.accessToken;

        try {
            URL url = new URL(this.baseUrl + this.mapToEndpoint(endpoint) + "?" + requiredParams + this.mapToParams(params));

            HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            connection.setRequestMethod(methodType);
            connection.setDoOutput(true);
            connection.setRequestProperty("Accept", "application/json");
            connection.setRequestProperty("Accept-Language", this.languageCode);
            
            InputStream in = new BufferedInputStream(connection.getInputStream());
            this.lastResponse = this.convertStreamToString(in);
            
            connection.disconnect();
            
            return this.lastResponse;
        } catch (IOException ex) {
            throw new RuntimeException(ex.getMessage());
        }
    }

    //
    // Helper methods
    //
    
    /**
     * Converts map to string with valid format for endpoint
     * @param map key-value pairs with endpoint configuration
     */
    private String mapToEndpoint(Map<String, String> map) {
        if (map.isEmpty()) {
            return "";
        }

        StringBuilder builder = new StringBuilder();
        for (String key : map.keySet()) {
            String value = map.get(key);
            try {
                builder.append(key != null ? URLEncoder.encode(key, "UTF-8") : "");
                builder.append(key != null ? "/" : "");
                builder.append(value != null ? URLEncoder.encode(value, "UTF-8") : "");
                builder.append(value != null ? "/" : "");
            } catch (UnsupportedEncodingException ex) {
                throw new RuntimeException(ex.getMessage());
            }
        }

        return this.removeLastCharFromString(builder.toString());
    }

    /**
     * Converts map to string with valid format for parameters
     * @param map key-value pairs with parameters configuration
     */
    private String mapToParams(Map<String, String> map) {
        if (map.isEmpty()) {
            return "";
        }

        StringBuilder builder = new StringBuilder();
        for (String key : map.keySet()) {
            if (builder.length() > 0) {
                builder.append("&");
            }
            String value = map.get(key);
            try {
                builder.append(key != null ? URLEncoder.encode(key, "UTF-8") : "");
                builder.append("=");
                builder.append(value != null ? URLEncoder.encode(value, "UTF-8") : "");
            } catch (UnsupportedEncodingException ex) {
                throw new RuntimeException(ex.getMessage());
            }
        }

        return builder.toString();
    }

    /**
     * Removes last '/' char from string
     */
    private String removeLastCharFromString(String param) {
        String result = param;
        if (param.length() > 0 && param.charAt(param.length() - 1) == '/') {
            result = param.substring(0, param.length() - 1);
        }
        return result;
    }

    /**
     * Converts InputStream to string
     */
    private String convertStreamToString(InputStream is) {
        java.util.Scanner s = new java.util.Scanner(is, "UTF-8").useDelimiter("\\A");
        return s.hasNext() ? s.next() : "";
    }

}
