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
