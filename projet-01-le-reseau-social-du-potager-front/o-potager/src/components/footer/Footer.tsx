import { FaFacebook, FaInstagram, FaTwitter } from "react-icons/fa";
import { Link } from "react-router-dom";

const Footer = () => {
  return (
    <div className="shadow-md bottom-0 w-full pb-4 flex flex-col bg-green-800 items-center">
      <div className="flex justify-center space-x-4 md:space-x-50 mt-4">
        <a
          href="#"
          className="text-white hover:underline flex flex-row items-center"
        >
          Mentions légales
        </a>
        <Link
          to="/contact"
          className="text-white hover:underline flex flex-row items-center"
        >
          Nous contacter
        </Link>
      </div>
      <div className="flex justify-center space-x-4 mt-4">
        <a href="#" className="text-white hover:underline">
          <FaFacebook size={24} />
        </a>
        <a href="#" className="text-white hover:underline">
          <FaInstagram size={24} />
        </a>
        <a href="#" className="text-white hover:underline">
          <FaTwitter size={24} />
        </a>
      </div>
      <div className="text-center mt-4 text-white">
        <span>O'Potager tous droits réservés</span>
      </div>
    </div>
  );
};

export default Footer;
