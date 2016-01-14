/*************************************************************
Copyright (C) Autodata Limited 1974-2015.
All Rights Reserved.
NOTICE: Code example provide to developers wishing to integrate with the
Autodata API. Only to be use under an NDA or valid contract with Autodata.
Autodata are not liable for this code being re-used or modified.
*/
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace SnippetApp
{
    class ManufacturerClass
    {
        private string manufacturer;
        private string id;

        public string Manufacturer
        {
            get { return manufacturer; }
            set { manufacturer = value; }
        }
        public string ID{
            get { return id; }
            set { id = value; }
        }

        public ManufacturerClass()
        {
            
        }
        public override string ToString() {
            return Manufacturer;
        }


    }
}
