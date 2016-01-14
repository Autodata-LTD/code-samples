/*************************************************************
Copyright (C) Autodata Limited 1974-2015.
All Rights Reserved.
NOTICE: Code example provide to developers wishing to integrate with the
Autodata API. Only to be use under an NDA or valid contract with Autodata.
Autodata are not liable for this code being re-used or modified.
*/
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
