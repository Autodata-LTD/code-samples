
public class ModelFamily {

	private String body;
	private String bodyname;
	private String subbody;

	protected String getBodyId() {
		return body;
	}

	/* Commented out to make it a read-only property
	protected void setBodyId(String bodyId) {
		this.body = bodyId;
	}*/

	protected String getBodyName() {
		return bodyname;
	}

	/* Commented out to make it a read-only property
	protected void setBodyName(String bodyName) {
		this.bodyname = bodyName;
	}*/

	protected String getSubBodyName() {
		return subbody;
	}

	/* Commented out to make it a read-only property
	protected void setSubBodyName(String subBodyName) {
		this.subbody = subBodyName;
	}*/
}
