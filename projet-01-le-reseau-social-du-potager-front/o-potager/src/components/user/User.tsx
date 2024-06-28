// @ts-nocheck

import React, { useEffect, useState } from "react";
import axios from "axios";
import Cookies from "js-cookie";
import { Link } from "react-router-dom";
import { AiOutlineClose } from "react-icons/ai";
import Account from "./Account";

const User = ({ isLogged, setIsLogged }) => {
  const [logs, setLogs] = useState({
    username: "",
    password: "",
  });

  const handlePassword = (e) => {
    const value = e.target.value;
    setLogs({ ...logs, password: value });
  };

  const handleUsername = (e) => {
    const value = e.target.value;
    setLogs({ ...logs, username: value });
  };

  const handleLogIn = async () => {
    event.preventDefault();

    try {
      const response = await axios.post(
        "http://floriancopez-server.eddi.cloud/opotager/api/login_check",
        {
          username: logs.username,
          password: logs.password,
        }
      );

      const token = response.data.token;
      const userId = response.data.data.id;
      const currentUser = response.data.data.username;
      Cookies.set("authToken", token, { sameSite: "None" });
      Cookies.set("userId", userId, { sameSite: "None" });
      Cookies.set("currentUser", currentUser, { SameSite: "None" });
      setIsLogged(true);
    } catch (error) {
      console.error(error);
      alert("une erreur est survenue");
    }
  };

  const handleLogOut = () => {
    Cookies.remove("authToken");
    Cookies.remove("userId");
    setIsLogged(false);
  };

  return !isLogged ? (
    <div className="flex flex-col h-screen w-full justify-center bg-zinc-50">
      <Link to="/">
        <div className="absolute top-2 right-2">
          <AiOutlineClose className="text-3xl" />
        </div>
      </Link>
      <form className="flex flex-col items-center gap-3 xl:flex" noValidate>
        <label htmlFor="email"></label>
        <input
          onChange={handleUsername}
          type="text"
          required
          placeholder="nom d'utilisateur"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-green-800"
        />
        <label htmlFor="password"></label>
        <input
          onChange={handlePassword}
          type="password"
          required
          placeholder="Mot de passe"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-green-800"
        />
        <button
          type="submit"
          onClick={handleLogIn}
          className="h-12 rounded-md w-80 bg-green-800 hover:bg-green-900 mt-40 text-white text-xl font-semibold p-7 flex items-center justify-center"
        >
          Connexion
        </button>
        <Link to="/register">
          <button
            type="button"
            className="h-12 rounded-md w-80 bg-green-800 hover:bg-green-900 text-white text-xl font-semibold p-7 flex items-center justify-center"
          >
            S'enregistrer
          </button>
        </Link>
      </form>
    </div>
  ) : (
    <Account handleLogOut={handleLogOut} />
  );
};

export default User;
