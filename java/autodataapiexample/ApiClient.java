package autodataapiexample;

import java.util.Map;

/**
 * Common API client interface.
 * This code is only for demonstration purposes.
 * @company Autodata Limited
 */
public interface ApiClient {
    String getCountryCode();
    void setCountryCode(String countryCode);

    String getLanguageCode();
    void setLanguageCode(String languageCode);
    
    String call(Map<String, String> endpoint, String methodType);
    String call(Map<String, String> endpoint, Map<String, String> params, String methodType);
    
    String getLastResponse();
}
