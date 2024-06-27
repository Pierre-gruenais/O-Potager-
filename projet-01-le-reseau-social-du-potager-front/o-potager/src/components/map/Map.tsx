// @ts-nocheck

import React, { useState, useEffect } from "react";
import axios from "axios";
import "leaflet/dist/leaflet.css";
import { MapContainer, TileLayer, Marker, Popup } from "react-leaflet";
import L from "leaflet";
import { Link } from "react-router-dom";

const Map: React.FC = () => {
  // Query pour stocker la requête utilisateur
  const [query, setQuery] = useState("");
  // Pour stocker les emplacements de jardins récupérés à partir de la BDD
  const [locations, setLocations] = useState([]);
  // Pour indiquer si la recherche a été effectuée
  const [searched, setSearched] = useState(false);
  // Compteur de résultats obtenus
  const [resultsCount, setResultsCount] = useState(0);
  // Ajout d'un nouvel état pour stocker les images
  const [pictures, setPictures] = useState([]);

  // Fonction fetchDataFromDatabase effectue la requête de l'utilisateur, les résultats sont transformés en JSON et renvoyés
  // La requête est déclarée en tant que paramètre de la fonction

  const fetchDataFromDatabase = async (searchQuery: string) => {
    try {
      const response = await axios.get(
        `http://floriancopez-server.eddi.cloud/opotager/api/gardens/search?city=${searchQuery}&dist=20`
      );

      if (!response.data.length) {
        throw new Error("Réponse vide du serveur");
      }

      return response.data;
    } catch (error) {
      console.error("Une erreur s'est produite lors de la requête :", error);
      throw error;
    }
  };

  const fetchPictures = async (locationId) => {
    try {
      const response = await axios.get(
        `http://floriancopez-server.eddi.cloud/opotager/api/gardens/${locationId}/pictures`
      );
      return response.data;
    } catch (error) {
      console.error(
        "Une erreur s'est produite lors de la récupération des images :",
        error
      );
      throw error;
    }
  };

  useEffect(() => {
    if (locations.length > 0) {
      const locationIds = locations.map((location) => location.id);
      const fetchImagesForLocations = async () => {
        try {
          const images = await Promise.all(
            locationIds.map((locationId) => fetchPictures(locationId))
          );
          setPictures(images);
          // Ajoutez ici le console.log pour afficher les URL des images
        } catch (error) {
          console.error(
            "Une erreur s'est produite lors de la récupération des images :",
            error
          );
        }
      };
      fetchImagesForLocations();
    }
  }, [locations]);

  // Fonction appelée lorsque l'utilisateur clique sur le bouton de recherche
  const handleSearch = async () => {
    console.log("Search button clicked");

    try {
      // Appel de la fonction pour récupérer les données depuis la BDD
      const fetchedLocations = await fetchDataFromDatabase(query);
      console.log("Fetched locations:", fetchedLocations);
      // Mise à jour de l'état avec les données récupérées
      setLocations(fetchedLocations);
      // Mettre à jour le nombre de résultats trouvés
      setResultsCount(fetchedLocations.length);
      // Indiquer que la recherche a été effectuée
      setSearched(true);
    } catch (error) {
      console.error("Une erreur s'est produite lors de votre recherche", error);

      setLocations([]);
      setSearched(false);
      // Réinitialise le nombre de résultats trouvés en cas d'erreur
      setResultsCount(0);
    }
  };

  return (
    <div className="grid lg:grid-cols-2 m-0 p-0 mt-20 h-full">
      <div className="p-2 bg-white z-0">
        {/* Carte */}
        <MapContainer
          center={[48.84766531962861, 2.347549501137932]}
          zoom={9}
          style={{ width: "100%", height: "100%" }}
        >
          <TileLayer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution="© OpenStreetMap contributors"
          />

          {locations.map((location, index) => (
            <Marker key={location.id} position={[location.lat, location.lon]}>
              <Popup>{location.title}</Popup>
            </Marker>
          ))}
        </MapContainer>
      </div>

      <div className="p-2 bg-white overflow-y-auto">
        <div className="text-center m-2">
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Renseigner une ville..."
            className="rounded border p-2"
            onKeyDown={(e) => {
              if (e.key === "Enter") {
                handleSearch();
              }
            }}
          />

          <button
            onClick={handleSearch}
            className="bg-green-800 text-white px-4 py-2 rounded ml-1"
          >
            Rechercher
          </button>
        </div>

        {searched && resultsCount > 0 ? (
          <div>
            <div className="m-2 h-8 ">
              <p>{resultsCount} résultat(s) trouvé(s)</p>
            </div>

            {locations.map((location, index) => (
              <div
                className="max-w-sm w-full lg:max-w-full lg:flex my-2 h-full lg:overflow-y-auto"
                key={index}
              >
                {pictures[index]?.[0] && (
                  <div
                    className="h-48 lg:h-auto lg:w-48 flex-none bg-cover rounded-l text-center overflow-hidden"
                    style={{
                      backgroundImage: `url(${pictures[index][0].url})`,
                    }}
                    title="Woman holding a mug"
                  ></div>
                )}

                <div className="border-r border-b border-l border-gray-400 lg:border-l-0 lg:border-t lg:border-gray-400 bg-white rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal">
                  <div className="mb-8">
                    <p className="text-sm text-gray-600 flex items-center">
                      {location.username}
                    </p>
                    <div className="text-gray-900 font-bold text-xl mb-2">
                      {location.title}
                    </div>
                    <p className="text-gray-700 text-base">
                      {location.description}
                    </p>
                  </div>

                  <div className="flex items-center">
                    <img
                      className="w-10 h-10 rounded-full mr-4"
                      src={location.avatar}
                      alt="Avatar of Jonathan Reinink"
                    />

                    <div className="text-sm">
                      <Link to={`/annonce/${location.id}`}>
                        <button className="bg-green-800 text-white px-4 py-2 text-center rounded text-left text-sm mt-2">
                          Voir l'annonce
                        </button>
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          "Aucun résultat trouvé"
        )}
      </div>
    </div>
  );
};

export default Map;
