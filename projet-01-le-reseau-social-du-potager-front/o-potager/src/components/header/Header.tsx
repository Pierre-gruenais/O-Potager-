// @ts-nocheck

import React, { useState } from "react";
import { Link } from "react-router-dom";
import { BiSolidUserCircle } from "react-icons/bi";
import { GiHamburgerMenu } from "react-icons/gi";
import { GiTomato } from "react-icons/gi";
const Header = () => {
  const navLinks = [
    { name: "TROUVEZ VOTRE JARDIN", link: "/trouvez-votre-jardin" },
    { name: "PRETEZ VOTRE JARDIN", link: "/pretez-votre-jardin" },
    { name: "NOTRE CONCEPT", link: "/concept" },
    { name: "CONTACT", link: "/contact" },
  ];

  const [burgerOpen, setBurgerOpen] = useState(false);
  const toggleBurger = () => {
    setBurgerOpen(() => false);
  };

  return (
    <div className="shadow-md fixed w-full top-0 left-0 z-50">
      <div className="md:flex items-center justify-between bg-green-800 py-4 md:px-10 px-3 content-center">
        <div className="flex ml-2">
          <Link to="/">
            <GiTomato className="h-10 w-10 text-white mr-5" />
          </Link>
          <Link to="/user">
            <BiSolidUserCircle className="w-10 h-10 text-white" />
          </Link>
        </div>
        <div
          onClick={() => setBurgerOpen(!burgerOpen)}
          className="absolute right-8 top-4 cursor-pointer md:hidden"
        >
          <GiHamburgerMenu className="w-10 h-10 text-white" />
        </div>
        <ul
          className={`md:flex md:items-center md:pb-0 pb-1 md:static bg-green-800 md:z-auto left-0 w-full md:w-auto md:pl-9 pl-4 ${
            burgerOpen ? "" : "hidden"
          }`}
        >
          {navLinks.map((navLink) => (
            <li key={navLink.name} className="md:ml-8 text-xl md:my-0 my-7">
              <Link
                onClick={toggleBurger}
                to={navLink.link}
                className="text-white md:hover:underline md:underline-offset-8 md:decoration-2"
              >
                {navLink.name}
              </Link>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default Header;
