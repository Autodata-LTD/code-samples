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
