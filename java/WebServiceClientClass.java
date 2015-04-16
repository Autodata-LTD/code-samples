import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

/**
 * 
 * @company Autodata Limited
 * The class WebServiceClientClass has been used as a client to invoke 
 * different methods in the API.
 */
public class WebServiceClientClass {

	/**
	 * List of required parameters
	 */
	private final static String API_KEY = "insert_your_api_key_here";
	private final static String API_BASE_URL = "http://api.autodata-group.com/";
	private final static String LANGUAGE_TAG = "en-GB";

	/**
	 * @param args
	 */
	public static void main(String[] args) {

		String queryString = "api_key=" + API_KEY;
		int option = 0;
		System.out.println("Choose one from the following options:");
		System.out.println("1. Get Manufacturer list");
		System.out.println("2. Get Model family list");
		System.out.println("3. Get Service operations for");
		
		try{
		    BufferedReader bufferRead = new BufferedReader(new InputStreamReader(System.in));
		    try{
	            option = Integer.parseInt(bufferRead.readLine());
	        } catch(NumberFormatException nfe) {
	            System.err.println("Invalid Format!");
	        }
		}
		catch(IOException ioe)
		{
			ioe.printStackTrace();
		}

		switch (option) {
			case 1:
				WebServiceApiClass.GetManufacturerList(API_BASE_URL, queryString, 
						LANGUAGE_TAG);
				break;
			case 2:
				String manufacturerId = "ALF0";
				WebServiceApiClass.GetModelFamilyList(API_BASE_URL, queryString, 
						LANGUAGE_TAG, manufacturerId);
				break;
			case 3:
				String scheduleId = "AUDSG0000278";
				int intervalId = 1;
				boolean listParts = false;
				WebServiceApiClass.GetServiceOperationList(API_BASE_URL, queryString, 
						LANGUAGE_TAG, scheduleId, intervalId, listParts);
				break;
			default:
				System.out.println("Invalid choice");
				break;
		}
	}
}