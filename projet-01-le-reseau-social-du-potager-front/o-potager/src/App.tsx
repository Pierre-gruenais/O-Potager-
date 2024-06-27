// @ts-nocheck

import React, { useState } from "react";
import { Routes, Route } from "react-router-dom";
import Header from "./components/header/Header";
import Main from "./components/main/Main";
import Footer from "./components/footer/Footer";
import FindYourGarden from "./components/findYourGarden/FindYourGarden";
import LendYourGarden from "./components/lendyourgarden/LendYourGarden";
import User from "./components/user/User";
import Register from "./components/user/Register";
import OurConcept from "./components/ourConcept/OurConcept";
import Contact from "./components/contact/Contact";
import Card from "./components/card/Card";

function App() {
  const [isLogged, setIsLogged] = useState(false);

  return (
    <>
      <div className=" bg-slate-300 w-full h-screen">
        <Routes>
          <Route
            path="/"
            element={
              <>
                <Header />
                <Main />
                <Footer />
              </>
            }
          />

          <Route
            path="/trouvez-votre-jardin"
            element={
              <>
                <Header />
                <FindYourGarden />
                <Footer />
              </>
            }
          />

          <Route
            path="/annonce/:locationId"
            element={
              <>
                <Header />
                <Card isLogged={isLogged} />
                <Footer />
              </>
            }
          />

          <Route
            path="pretez-votre-jardin"
            element={
              <>
                <Header />
                <LendYourGarden />
                <Footer />
              </>
            }
          />
          <Route
            path="/concept"
            element={
              <>
                <Header />
                <OurConcept />
                <Footer />
              </>
            }
          />
          <Route
            path="/contact"
            element={
              <>
                <Header />
                <Contact />
                <Footer />
              </>
            }
          />
          <Route
            path="/user"
            element={<User isLogged={isLogged} setIsLogged={setIsLogged} />}
          />
          <Route
            path="/register"
            element={<Register isLogged={isLogged} setIsLogged={setIsLogged} />}
          />
          <Route
            path="/concept"
            element={
              <>
                <Header />
                <OurConcept />
                <Footer />
              </>
            }
          />

          <Route
            path="/contact"
            element={
              <>
                <Header />
                <Contact />
                <Footer />
              </>
            }
          />
        </Routes>
      </div>
    </>
  );
}

export default App;
