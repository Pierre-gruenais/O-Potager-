// @ts-nocheck

import React, { useEffect, useState } from "react";
import { IKContext, IKUpload } from "imagekitio-react";
import axios from "axios";
import { AiOutlineClose } from "react-icons/ai";
import { Link } from "react-router-dom";

const Register = () => {
  const [formData, setFormData] = useState({
    username: "",
    email: "",
    password: "",
    phone: "",
    avatar: "",
    roles: [],
  });

  const publicKey = "public_mOe2SQYQyudsmyQ9EFcbH0SqgEk=";
  const urlEndpoint = "https://ik.imagekit.io/opotager/";

  const authenticator = async () => {
    try {
      const response = await fetch(
        "http://floriancopez-server.eddi.cloud/opotager/imagekit/auth"
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(
          `Request failed with status ${response.status}: ${errorText}`
        );
      }

      const data = await response.json();
      const { signature, expire, token } = data;
      return { signature, expire, token };
    } catch (error) {
      throw new Error(`Authentication request failed: ${error.message}`);
    }
  };

  const onError = (err) => {
    console.log("Error", err);
  };

  const onSuccess = (res) => {
    console.log("Success", res.url);
    const url = res.url;
    setFormData((prevData) => ({
      ...prevData,
      avatar: url,
    }));
  };

  useEffect(() => {
    console.log(formData);
  }, [formData]);

  const handleChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
  };

  useEffect(() => {
    console.log(formData);
  }, [formData]);

  const handleSubmitForm = async (event) => {
    event.preventDefault();

    try {
      const response = await axios.post(
        "http://floriancopez-server.eddi.cloud/opotager/api/users/",
        formData
      );

      alert("vous êtes enregistré");
    } catch (error) {
      console.error(error);
      alert(`une erreur s'est produite`);
    }
  };

  return (
    <div className="flex flex-col h-screen w-full justify-center bg-zinc-50">
      <Link to="/user">
        <div className="absolute top-2 right-2">
          <AiOutlineClose className=" text-3xl" />
        </div>
      </Link>
      <form
        onSubmit={handleSubmitForm}
        className="flex flex-col items-center gap-3"
      >
        <label htmlFor="username"></label>
        <input
          name="username"
          onChange={handleChange}
          type="text"
          placeholder="nom d'utilisateur"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 focus:border-green-800 p-7 outline-none"
        />
        <label htmlFor="email"></label>
        <input
          name="email"
          onChange={handleChange}
          type="email"
          placeholder="email"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 focus:border-green-800 p-7 outline-none"
        />
        <label htmlFor="password"></label>
        <input
          name="password"
          onChange={handleChange}
          type="password"
          placeholder="mot de passe"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 focus:border-green-800 p-7 outline-none"
        />
        <label htmlFor="phone"></label>
        <input
          name="phone"
          onChange={handleChange}
          type="phone"
          placeholder="numéro de téléphone"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 focus:border-green-800 p-7 outline-none"
        />
        <IKContext
          publicKey={publicKey}
          urlEndpoint={urlEndpoint}
          authenticator={authenticator}
        >
          <label htmlFor="avatar" className="font-bold">
            Ajoutez un avatar
          </label>
          <IKUpload
            className=" w-80 bg-white rounded-md border-2 border-zinc-500 focus:border-green-800 p-7 outline-none"
            fileName="test-upload-avatar.png"
            onSuccess={onSuccess}
            onError={onError}
          />
        </IKContext>
        <button
          type="submit"
          className="h-12 rounded-md w-80 bg-green-800 hover:bg-green-900 mt-14 text-white text-xl font-semibold p-7 flex items-center justify-center"
        >
          S'enregistrer
        </button>
      </form>
    </div>
  );
};

export default Register;
