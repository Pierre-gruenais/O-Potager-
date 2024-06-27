// @ts-nocheck

import React, { useState, useEffect } from "react";
import axios from "axios";
import { useParams, useNavigate } from "react-router-dom";
import { MapContainer, TileLayer, Marker, Popup } from "react-leaflet";
import { AiOutlineClose } from "react-icons/ai";

const Card: React.FC = () => {
  const { locationId } = useParams();
  const [location, setLocation] = useState(null);
  const [pictures, setPictures] = useState([]);
  const navigate = useNavigate();

  useEffect(() => {
    fetchData(locationId);
  }, [locationId]);

  const fetchData = async (locationId) => {
    try {
      const response = await axios.get(
        `http://floriancopez-server.eddi.cloud/opotager/api/gardens/${locationId}`
      );
      const data = response.data;
      setLocation(data);

      const pictureResponse = await axios.get(
        `http://floriancopez-server.eddi.cloud/opotager/api/gardens/${locationId}/pictures`
      );
      const pictureData = pictureResponse.data;
      setPictures(pictureData);
    } catch (error) {
      console.error(
        "Une erreur s'est produite lors de la récupération des données :",
        error
      );
    }
  };

  return (
    <div className="p-2 lg:p-8 mt-20 bg-zinc-50">
      <button onClick={() => navigate(-1)}>
        <AiOutlineClose className="w-10 h-10" />
      </button>
      {location && (
        <>
          <div>
            <h1 className="text-3xl font-semibold">{location.title}</h1>
            <p className="text-gray-600 mt-2">{location.description}</p>
          </div>

          <div className="lg:grid lg:grid-cols-2 gap-8 my-8">
            <div>
              <div className="mt-4">
                <h2 className="text-xl font-semibold">
                  Informations sur le jardin
                </h2>
                <ul className="  mt-2">
                  <li>
                    Adresse: {location.address}, {location.postalCode}{" "}
                    {location.city}
                  </li>
                  <li>Eau disponible: {location.water ? "Oui" : "Non"}</li>
                  <li>Outils disponibles: {location.tool ? "Oui" : "Non"}</li>
                  <li>Abri de jardin: {location.shed ? "Oui" : "Non"}</li>
                  <li>
                    Culture déjà en cour: {location.cultivation ? "Oui" : "Non"}
                  </li>
                  <li>Surface: {location.surface} m²</li>
                </ul>
              </div>

              <div className="mt-4">
                <h2 className="text-xl font-semibold">Contact</h2>
                <div className="flex items-center mt-2">
                  <img
                    className="w-10 h-10 rounded-full mr-2"
                    src={location.user.avatar}
                    alt={location.user.username}
                  />
                  <p>{location.user.username}</p>
                </div>
                <p className="text-gray-600 mt-2">
                  {location.user.email}, {location.user.phone}
                </p>
              </div>

              <div className="mt-4">
                <h2 className="text-xl font-semibold">Photos du jardin</h2>
                <div className="flex flex-wrap justify-center lg:justify-start lg:flex-none lg:flex-nowrap gap-2 mt-2">
                  {pictures.map((picture) => (
                    <div className="w-1/2 lg:w-auto" key={picture.id}>
                      <img
                        src={picture.url}
                        alt={location.title}
                        className="w-full h-32 lg:h-48 lg:w-48 object-cover"
                      />
                    </div>
                  ))}
                </div>
              </div>
            </div>

            <div className="mt-4 h-full z-0">
              <MapContainer
                center={[location.lat, location.lon]}
                zoom={13}
                className="w-full h-full"
              >
                <TileLayer
                  url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                  attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                />
                <Marker position={[location.lat, location.lon]}>
                  <Popup>{location.title}</Popup>
                </Marker>
              </MapContainer>
            </div>
          </div>
        </>
      )}
    </div>
  );
};

export default Card;
