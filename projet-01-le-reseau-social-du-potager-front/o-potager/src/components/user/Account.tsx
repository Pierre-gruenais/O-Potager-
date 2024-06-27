// @ts-nocheck

import React from "react";
import { Link } from "react-router-dom";
import { AiOutlineClose } from "react-icons/ai";
import Cookies from "js-cookie";

const Account = ({ handleLogOut }) => {
  const currentUser = Cookies.get("currentUser");
  return (
    <div className="flex flex-col h-screen w-full justify-center bg-zinc-50">
      <Link to="/">
        <div className="absolute top-2 right-2">
          <AiOutlineClose className=" text-3xl" />
        </div>
      </Link>
      <Link to="/" className="flex justify-center">
        <h1 className="font-bold">Bienvenu {currentUser} !</h1>
      </Link>
      <Link to="/" className="flex justify-center bottom-3 mt-9">
        <button
          onClick={handleLogOut}
          type="button"
          className="h-12 rounded-md w-80 border-zinc-50 hover:border-green-900 bg-green-800 text-white text-xl font-semibold p-7 flex items-center justify-center"
        >
          Déconnexion
        </button>
      </Link>
    </div>
  );
};

export default Account;
