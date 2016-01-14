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
    class ModelFamilyClass
    {
        private string body;
        private string bodyname;
        private string subbody;

        public string Body
        {
            get { return body; }
            set { body = value; }
        }
        public string BodyName
        {
            get { return bodyname; }
            set { bodyname = value; }
        }
        public string SubBody
        {
            get { return subbody; }
            set { subbody = value; }
        }

        public ModelFamilyClass()
        {
            
        }

        public override string ToString() {
            return BodyName+" "+SubBody;
        }
    }
}
