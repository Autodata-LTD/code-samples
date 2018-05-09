/**
 * Copyright (C) Autodata Limited 1974-2018.
 * All Rights Reserved.
 * NOTICE: Code example provide to developers wishing to integrate with the
 * Autodata API. Only to be use under an NDA or valid contract with Autodata.
 * Autodata are not liable for this code being re-used or modified.
 */

var valid = 0;

/**
 * Simple class providing access to Autodata API
 */
function Api() {
    this.baseUrl       = 'https://api.autodata-group.com';
    this.version       = 'v1';
    this.tokenUrl      = 'https://account.autodata-group.com/oauth/access_token';
    this.grant_type    = 'client_credentials';
    this.client_id     = '';
    this.client_secret = '';
    this.scope         = 'scope1';
    this.state         = '123456789';
    this.countryCode   = 'gb';
    this.language      = 'en-gb';
    this.format        = 'application/json';

    this.response = {
        success: false,
        status: 200,
        responseText: '',
        statusText: ''
    };

    /**
     * Initiates basic variables for the API class
     */
    this.init = function (baseUrl, version, apiKey) {
        this.setBaseUrl(baseUrl);
        this.setVersion(version);
        this.setApiKey(apiKey);
    };

    this.setBaseUrl = function (baseUrl) {
        this.baseUrl = baseUrl;
    };

    this.setVersion = function (version) {
        this.version = version;
    };

    this.setApiKey = function (apiKey) {
        this.apiKey = apiKey;
    };

    this.getToken = function () {
        if (Date.now() > valid) {
            var request             = new XMLHttpRequest();
            var params              = Array();
            params['grant_type']    = this.grant_type;
            params['client_id']     = this.client_id;
            params['client_secret'] = this.client_secret;
            params['scope']         = this.scope;
            params['state']         = this.state;

            var arParameters = Array();
            for (var param in params) {
                if (params.hasOwnProperty(param)) {
                    arParameters.push(param + '=' + params[param]);
                }
            }

            var strParameters = '?' + arParameters.join('&');
            var url           = this.tokenUrl + strParameters;

            request.open('POST', url, false);
            request.send();

            this.response.status  = request.status;
            this.response.success = (request.status == 200);

            if (this.response.success) {
                this.response.responseText = request.responseText;
                var json                   = this.decodeResponse();
                token                      = json['access_token'];
                valid                      = (json['expires_in'] * 1) + Date.now();
            }
            else {
                alert('Connection Failed');
                return false;
            }
        }
        return token;
    }

    /**
     * Makes the actual call to the API endpoint
     */
    this.call = function (url, method, params) {

        if (typeof params == 'undefined' || params == null) {
            params = Array();
        }

        params['access_token'] = this.getToken();
        params['country-code'] = this.countryCode;
        params['language']     = this.language;

        var arParameters = Array();
        for (var param in params) {
            if (params.hasOwnProperty(param)) {
                arParameters.push(param + '=' + params[param]);
            }
        }

        var strParameters = '?' + arParameters.join('&');
        url               = url + strParameters;

        var apiResponseObject = this.response;

        $.ajax({
            async: false,
            dataType: 'text',
            url: url,
            method: method,
        }).done(function (data, textStatus, jqXHR) {
            apiResponseObject.responseText = data;
            apiResponseObject.success      = true;
            apiResponseObject.status       = jqXHR.status;

            return apiResponseObject;
        }).fail(function (jqXHR, textStatus) {
            apiResponseObject.statusText = textStatus;
            apiResponseObject.status     = jqXHR.status;
        });

        return apiResponseObject;
    };

    /**
     * Decodes json response and returns a list if objects for further processing in JavaScript code
     */
    this.decodeResponse = function () {
        var json = this.response.responseText;
        return JSON && JSON.parse(json);
    };

    /**
     * Calls the manufacturer API and returns a list of manufacturers
     */
    this.getManufacturers = function () {
        var apiData = this.call(this.baseUrl + '/' + this.version + '/manufacturers', 'GET', null);

        if (apiData.success) {
            return this.decodeResponse();
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };

    /**
     * Calls the manufacturer API and returns a list of models for given manufacturer
     */
    this.getManufacturer = function (manufacturer) {
        var apiData = this.call(this.baseUrl + '/' + this.version + '/manufacturers/' + manufacturer, 'GET', null);

        if (apiData.success) {
            return this.decodeResponse();
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };

    /**
     * Calls the vehicle API and returns vehicles details
     */
    this.getVehicles = function (manufacturer, model_id) {
        var params                = [];
        params['manufacturer_id'] = manufacturer;
        params['model_id']        = model_id;

        var apiData = this.call(this.baseUrl + '/' + this.version + '/vehicles', 'GET', params);

        if (apiData.success) {
            return this.decodeResponse();
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };

    /**
     * Calls the vehicle API and returns vehicle details
     */
    this.getVehicle = function (mid) {
        var apiData = this.call(this.baseUrl + '/' + this.version + '/vehicles/' + mid, 'GET', null);

        if (apiData.success) {
            var json = this.decodeResponse();
            return JSON.stringify(json, null, '\t');
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };

    /**
     * Calls the vehicle API and returns vehicle details
     */
    this.getRepair = function (mid) {
        var apiData = this.call(this.baseUrl + '/' + this.version + '/vehicles/' + mid + '/repair-times', 'GET', null);

        if (apiData.success) {
            var json = this.decodeResponse();
            return JSON.stringify(json, null, '\t');
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };

    /**
     * Calls the vehicle API and returns vehicle details
     */
    this.getRepairTimes = function (mid, repair_times_id, parts) {
        var params      = [];
        params['parts'] = parts;

        var apiData = this.call(this.baseUrl + '/' + this.version + '/vehicles/' + mid + '/repair-times/' + repair_times_id, 'GET', params);

        if (apiData.success) {
            var json = this.decodeResponse();
            return JSON.stringify(json, null, '\t');
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };

    /**
     * Calls the vehicle API and returns vehicle details
     */
    this.getService = function (mid) {
        var apiData = this.call(this.baseUrl + '/' + this.version + '/vehicles/' + mid + '/service-schedules', 'GET', null);

        if (apiData.success) {
            var json = this.decodeResponse();
            return JSON.stringify(json, null, '\t');
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };

    /**
     * Calls the vehicle API and returns vehicle details
     */
    this.getServiceSchedule = function (mid, variant_id, parts) {
        var params      = [];
        params['parts'] = parts;

        var apiData = this.call(this.baseUrl + '/' + this.version + '/vehicles/' + mid + '/service-schedules/' + variant_id, 'GET', params);

        if (apiData.success) {
            var json = this.decodeResponse();
            return JSON.stringify(json, null, '\t');
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };

    /**
     * Calls the vehicle API and returns vehicle details
     */
    this.getServiceIntervals = function (mid, variant_id, interval_id) {
        var apiData = this.call(this.baseUrl + '/' + this.version + '/vehicles/' + mid + '/service-schedules/' + variant_id + '/intervals/' + interval_id, 'GET', null);

        if (apiData.success) {
            var json = this.decodeResponse();
            return JSON.stringify(json, null, '\t');
        } else {
            console.log(apiData.statusText);
            return false;
        }
    };
}
