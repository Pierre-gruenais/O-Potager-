// @ts-nocheck

import React, { useEffect, useState } from "react";
import { IKContext, IKUpload } from "imagekitio-react";
import axios from "axios";
import Cookies from "js-cookie";

const LendYourGarden = () => {
  const [formData, setFormData] = useState({
    title: "",
    description: "",
    address: "",
    postal_code: "",
    city: "",
    water: false,
    shed: false,
    tool: false,
    cultivation: false,
    state: "",
    surface: 0,
    phone_access: false,
    pictures: [],
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
    console.log("Success", res);
    const newImage = { url: res.url };
    setFormData((prevData) => ({
      ...prevData,
      pictures: [...prevData.pictures, newImage],
    }));
  };

  const handleSubmit = async (event: any) => {
    event.preventDefault();

    const token = Cookies.get("authToken");
    const userId = Cookies.get("userId");
    const userIdInt = parseInt(userId, 10);

    setFormData((prevData) => ({
      ...prevData,
      user: userIdInt,
    }));

    try {
      const response = await axios.post(
        "http://floriancopez-server.eddi.cloud/opotager/api/gardens/",
        formData,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        }
      );

      console.log(response);
    } catch (error) {
      console.error(error);
    }
  };

  useEffect(() => {
    console.log(formData);
  }, [formData]);

  const handleChange = (event) => {
    const { name, value, type, checked } = event.target;
    const newValue =
      name === "surface"
        ? parseInt(value)
        : type === "checkbox"
        ? checked
        : value;
    setFormData((prevData) => ({
      ...prevData,
      [name]: newValue,
    }));
  };

  return (
    <div className="flex w-full justify-center bg-zinc-50 flex-grow overflow-y-auto pt-20 pb-40">
      <form
        onSubmit={handleSubmit}
        className="flex flex-col items-center gap-3"
      >
        <label htmlFor="title"></label>
        <input
          onChange={handleChange}
          type="text"
          id="title"
          name="title"
          placeholder="Titre du jardin"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
        />

        <label htmlFor="description" className="font-semibold"></label>
        <input
          onChange={handleChange}
          type="text"
          id="description"
          name="description"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
          placeholder="Description du jardin"
        />

        <label htmlFor="address" className="font-semibold"></label>
        <input
          onChange={handleChange}
          type="text"
          id="address"
          name="address"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
          placeholder="Votre adresse"
        />

        <label htmlFor="postal_code" className="font-semibold"></label>
        <input
          onChange={handleChange}
          type="text"
          id="postal_code"
          name="postal_code"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
          placeholder="Votre code postal"
        />

        <label htmlFor="city" className="font-semibold"></label>
        <input
          onChange={handleChange}
          type="text"
          id="city"
          name="city"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
          placeholder="Ville"
        />

        <label htmlFor="water" className="font-semibold">
          Votre jardin possède t'il un point d'eau ?
        </label>
        <input
          onChange={handleChange}
          type="checkbox"
          id="water"
          name="water"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
        />

        <label htmlFor="shed" className="font-semibold">
          Votre jardin possède t'il un abri ?
        </label>
        <input
          onChange={handleChange}
          type="checkbox"
          id="shed"
          name="shed"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
        />

        <label htmlFor="tool" className="font-semibold">
          Votre jardin possède t'il des outils ?
        </label>
        <input
          onChange={handleChange}
          type="checkbox"
          id="tool"
          name="tool"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
        />

        <label htmlFor="cultivation" className="font-semibold">
          Votre jardin est il cultivé ?
        </label>
        <input
          onChange={handleChange}
          type="checkbox"
          id="cultivation"
          name="cultivation"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
        />

        <label htmlFor="state" className="font-semibold">
          Dans quel état est votre jardin ?
        </label>
        <input
          onChange={handleChange}
          type="text"
          id="state"
          name="state"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
        />

        <label htmlFor="surface" className="font-semibold">
          Quelle surface souhaitez-vous prêter ?
        </label>
        <input
          onChange={handleChange}
          type="number"
          id="surface"
          name="surface"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
        />

        <label htmlFor="phone_access" className="font-semibold">
          Souhaitez vous que votre numéro apparaisse dans les annonces ?
        </label>
        <input
          onChange={handleChange}
          type="checkbox"
          id="phone_access"
          name="phone_access"
          className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
        />

        <div className="flex flex-col pb-2 items-center">
          <IKContext
            publicKey={publicKey}
            urlEndpoint={urlEndpoint}
            authenticator={authenticator}
          >
            <label htmlFor="pictures" className="font-semibold">
              Ajoutez une photo :
            </label>
            <IKUpload
              className="h-12 rounded-md w-80 border-2 border-zinc-500 p-7 outline-none focus:border-amber-400"
              fileName="test-upload.png"
              onSuccess={onSuccess}
              onError={onError}
            />
          </IKContext>
        </div>
        <button
          type="submit"
          className="h-12 rounded-md w-80 bg-green-800 hover:bg-green-900 text-white text-xl font-semibold p-7 flex items-center justify-center mt-10"
        >
          Valider
        </button>
      </form>
    </div>
  );
};

export default LendYourGarden;
