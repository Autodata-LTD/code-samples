/*************************************************************
Copyright (C) Autodata Limited 1974-2015.
All Rights Reserved.
NOTICE: Code example provide to developers wishing to integrate with the
Autodata API. Only to be use under an NDA or valid contract with Autodata.
Autodata are not liable for this code being re-used or modified.
*/
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.IO;
using HttpUtils;
using Newtonsoft.Json;

namespace SnippetApp
{
    public partial class Form1 : Form
    {
        private const string API_KEY = "api_key=7t8ss7j9at3xt9d7azf6vqjh";
        private const string API_BASE_URL = "http://api.autodata-group.com/integration/";

        private const string MANUFACTURER_ENDPOINT = "v0/manufacturer";
        private const string MODELFAMILY_ENDPOINT = "v0/modelfamily";
        private const string SERVICE_OPERATIONS_ENDPOINT = "v0/services/operations";
           
        /// <summary>
        /// Calls the specified API
        /// </summary>
        /// <param name="apiURL">The complete URL for the request including the query string and api key</param>
        /// <param name="language">The Language culture name for the returned data e.g. "en-gb"</param>
        /// <param name="success">Returns true if no error is returned from the API</param>
        /// <returns>The requested data</returns>
        private string CallAPI(string apiURL, string language, out bool success)
        {
            string response = "";
            Stream webStream;
            HttpWebResponse webResponse;
            success = false;

            HttpWebRequest webRequest = (HttpWebRequest)WebRequest.Create(apiURL);
            //Set the Accept-Language header to the required language for language dependent information
            webRequest.Headers.Add(HttpRequestHeader.AcceptLanguage, language);
            webRequest.Method = "GET";

            try
            {
                webResponse = (HttpWebResponse)webRequest.GetResponse();
                success = (webResponse.StatusCode == HttpStatusCode.OK);
                webStream = webResponse.GetResponseStream();
            }
            catch (WebException e)
            {
                //Capture the error message
                webStream = e.Response.GetResponseStream();
            }

            //Return the response data/error message as a string
            StreamReader responseReader = new StreamReader(webStream);
            response = responseReader.ReadToEnd();
            return response;
        }



        public Form1()
        {
            InitializeComponent();
        }


        /// <summary>
        /// Calls the manufacturers API and adds the list of ManufacturerClass objects to a combobox 
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void btnManufacturers_Click(object sender, EventArgs e)
        {
            cbModelFamily.Items.Clear();
            bool success;
            string apiData = CallAPI(API_BASE_URL + MANUFACTURER_ENDPOINT + "?" + API_KEY, "en-gb", out success);
            if (success)
            {
                ManufacturerClass[] manufacturers = JsonConvert.DeserializeObject<ManufacturerClass[]>(apiData);
                foreach (ManufacturerClass manufacturer in manufacturers)
                {
                    cbManufacturers.Items.Add(manufacturer);
                }
                cbManufacturers.SelectedIndex = 0;
            }
            else {
                MessageBox.Show(apiData);
            }

        }


        /// <summary>
        /// Calls the Model Family API and adds the list of ModelFamilyClass objects to a combobox
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void cbManufacturers_SelectedIndexChanged(object sender, EventArgs e)
        {
            cbModelFamily.Items.Clear();
            bool success;
            ManufacturerClass manufacturer = (ManufacturerClass)(cbManufacturers.Items[cbManufacturers.SelectedIndex]);
            string apiData = CallAPI(API_BASE_URL + MODELFAMILY_ENDPOINT + "?manufacturer=" + manufacturer.ID + "&" + API_KEY, "en-gb", out success);
            if (success)
            {
                ModelFamilyClass[] modelFamilies = JsonConvert.DeserializeObject<ModelFamilyClass[]>(apiData);
                if (modelFamilies.Length > 0)
                {
                    foreach (ModelFamilyClass modelFamily in modelFamilies)
                    {
                        cbModelFamily.Items.Add(modelFamily);
                    }
                    cbModelFamily.SelectedIndex = 0;
                }
            }
            else
            {
                MessageBox.Show(apiData);
            }

        }


        /// <summary>
        /// Calls the Service Operations API and adds the returned data to a textbox
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void btnServiceOperations_Click(object sender, EventArgs e)
        {
            bool success;
            string apiData = CallAPI(API_BASE_URL + SERVICE_OPERATIONS_ENDPOINT + "/AUDSG1900131/1" + "?" + API_KEY, "en-gb", out success);
            if (success)
                textBox1.Text = apiData;
            else
                MessageBox.Show(apiData);
        }

 

    }
}
