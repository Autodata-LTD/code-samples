import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

// google-gson is a Java library that has been used 
// to convert JSON representation to Java Objects.
// The library could be downloaded from 
// https://code.google.com/p/google-gson/
import com.google.gson.Gson;

/**
 * 
 * @company Autodata Limited
 * The class WebServiceApiClass is the collection of 
 * methods used for consuming web services.
 * This code is only for demonstration purposes.
 */
public class WebServiceApiClass {

	/**
	 * List of end points used in the code snippet for 
	 */
	private final static String MANUFACTURER_ENDPOINT = "v0/manufacturer";
	private final static String MODELFAMILY_ENDPOINT = "v0/modelfamily";
	private final static String SERVICE_OPERATIONS_ENDPOINT = "v0/services/operations";
	private final static String GET_METHOD = "GET";

	/**
	 * The method calls the manufacturers API and converts the JSON response into
	 * an array of Manufacturer objects and outputs the object properties
	 * to the console
	 * @param apiBaseUrl
	 * @param queryString
	 * @param languageTag
	 */
	public static void GetManufacturerList(String apiBaseUrl, String queryString, 
			String languageTag) {

		String manufacturerList = CallApi(apiBaseUrl, MANUFACTURER_ENDPOINT, 
				GET_METHOD, queryString, languageTag);
		Gson gson = new Gson();
		Manufacturer[] manufacturerArr = gson.fromJson(manufacturerList, Manufacturer[].class);
		if (manufacturerArr != null) {
			for (Manufacturer manu : manufacturerArr) {
				System.out.print(manu.getManufacturerName());
				System.out.print("\t");
				System.out.println(manu.getManufacturerId());
			}
		}
	}

	/**
	 * The method calls the Model family API and converts the JSON response into
	 * an array of ModelFamily objects and outputs the object properties
	 * to the console
	 * @param apiBaseUrl
	 * @param queryString
	 * @param languageTag
	 * @param manufacturerId
	 */
	public static void GetModelFamilyList(String apiBaseUrl, String queryString, 
			String languageTag, String manufacturerId) {

		queryString = new StringBuilder().append("manufacturer=").append(manufacturerId).append("&").append(queryString).toString();

		String modelFamilyList = CallApi(apiBaseUrl, MODELFAMILY_ENDPOINT, 
				GET_METHOD, queryString, languageTag);
		Gson gson = new Gson();
		ModelFamily[] modelFamilyArr = gson.fromJson(modelFamilyList, ModelFamily[].class);
		if (modelFamilyArr != null) {
			for (ModelFamily modelFamily : modelFamilyArr) {
				System.out.print(modelFamily.getBodyId());
				System.out.print("\t");
				System.out.print(modelFamily.getBodyName());
				System.out.print("\t");
				System.out.println(modelFamily.getSubBodyName());
			}
		}
	}

	/**
	 * The method calls the Service operations API and output the JSON string
	 * to the console
	 * properties to the console
	 * @param apiBaseUrl
	 * @param queryString
	 * @param languageTag
	 * @param scheduleId
	 * @param intervalId
	 * @param listParts
	 */
	public static void GetServiceOperationList(String apiBaseUrl, String queryString, 
			String languageTag, String scheduleId, int intervalId, boolean listParts) {

		queryString = new StringBuilder().append("parts=").append(listParts).append("&").append(queryString).toString();
		String endPoint = new StringBuilder().append(SERVICE_OPERATIONS_ENDPOINT).append("/").append(scheduleId).append("/").append(intervalId).toString();

		String serviceOperationList = CallApi(apiBaseUrl, endPoint, GET_METHOD, 
				queryString, languageTag);
		System.out.println(serviceOperationList);
	}

	/**
	 * The method consumes the web service and return a JSON string 
	 * to the caller
	 * @param apiBaseUrl
	 * @param endPoint
	 * @param methodType
	 * @param queryString
	 * @param languageTag
	 * @return [Output] String
	 */
	private static String CallApi(String apiBaseUrl, String endPoint, String methodType, 
			String queryString, String languageTag) {

		StringBuilder sb = new StringBuilder();
		try {
			URL apiUrl = new URL(apiBaseUrl + endPoint + "?" + queryString);
			HttpURLConnection webConn = (HttpURLConnection) apiUrl.openConnection();
			webConn.setRequestMethod(methodType);
			webConn.setRequestProperty("Accept", "application/json");
			webConn.setRequestProperty("Accept-Language", languageTag);

			if (webConn.getResponseCode() != 200) {
				throw new RuntimeException("Failed : HTTP error code : " + webConn.getResponseCode());
			}

			BufferedReader br = new BufferedReader(new InputStreamReader((webConn.getInputStream())));
			String line;
			while ((line = br.readLine()) != null) {
				sb.append(line);
			}

			webConn.disconnect();
		} catch (MalformedURLException murle) {
			// TODO Auto-generated catch block
			murle.printStackTrace();
		} catch (IOException ioe) {
			// TODO Auto-generated catch block
			ioe.printStackTrace();
		}

		return sb.toString();
	}
}
