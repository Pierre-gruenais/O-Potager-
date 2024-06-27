// @ts-nocheck

import React, { useState } from "react";

const Contact = () => {
  const [isMessageSent, setIsMessageSent] = useState(false);
  // handleFormSubmit pour envoyer a l'utilisateur un retour de confirmation de la réception de son message
  const handleFormSubmit = (e) => {
    e.preventDefault();
    setIsMessageSent(true);
  };

  return (
    <div className=" bg-zinc-50 flex flex-col items-center mt-20 w-screen h-screen">
      <h1 className="text-2xl font-bold mb-4">Contactez-nous</h1>

      <div className="mb-6">
        <h2 className="text-xl font-semibold mb-2">Informations de Contact</h2>
        <p>Adresse : 123 Rue du légume, Crosne, 91191</p>
        <p>Téléphone : +33 1 23 45 67 89</p>
        <p>Email : Contact@Opotager.com</p>
        <br />
        <p>Via le formulaire ci-dessous</p>
      </div>
      {isMessageSent ? (
        <div className="text-green-600 font-semibold mb-4">Message envoyé</div>
      ) : (
        <form onSubmit={handleFormSubmit}>
          <div className="mb-4">
            <label htmlFor="name" className="block font-semibold mb-2">
              Votre nom :
            </label>
            <input
              type="text"
              id="name"
              name="name"
              className="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-800"
            />
          </div>
          {/*Pour l'email, il faudra implémenter une logique de traitement côté serveurqui enverra reellement le msg à l'adresse email de reception */}
          <div className="mb-4">
            <label htmlFor="email" className="block font-semibold mb-2">
              Votre email :
            </label>
            <input
              type="email"
              id="email"
              name="email"
              className="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-800"
            />
          </div>
          <div className="mb-4">
            <label htmlFor="message" className="block font-semibold mb-2">
              Votre message :
            </label>
            <textarea
              id="message"
              name="message"
              className="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-800"
              rows="4"
            />
          </div>
          <button
            type="submit"
            className="bg-green-800 text-white px-4 py-2 rounded-lg hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-900"
          >
            Envoyer
          </button>
        </form>
      )}
    </div>
  );
};

export default Contact;
