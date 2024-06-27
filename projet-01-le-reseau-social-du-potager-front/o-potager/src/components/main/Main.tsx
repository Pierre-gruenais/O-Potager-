// @ts-nocheck

import React, { useState } from "react";

const Main = () => {
  return (
    <div className="bg-zinc-50 mt-16 pt-2 pb-6 z-0">
      <div className="relative">
        <img
          src="/garden-homepage.jpg"
          className="w-screen object-cover h-72 md:h-[500px] z-0"
        ></img>
        <div className="absolute inset-0 flex justify-center items-center md:mx-72">
          <input
            placeholder="Saisissez votre recherche"
            className=" h-12 border-2 focus:outline-green-800 focus:outline-4 focus:outline-offset-0 rounded-full p-2 focus:outline-none bg-none md:w-[350px]"
          />
        </div>
      </div>
      <div className="mt-12 px-6 md:flex">
        <div className="flex flex-col items-center my-12 md:w-1/3">
          <img
            src="/sprout-svgrepo-com.png"
            className="w-12 h-12 md:w-20 md:h-20"
          />
          <p className="mt-6 text-justify md:px-14">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio
            alias culpa corrupti! Commodi harum id quos, sapiente reprehenderit
            quasi hic modi dolorum dolores aliquam velit voluptate at aut maxime
            ducimus.
          </p>
        </div>
        <div className="flex flex-col items-center my-12 md:w-1/3">
          <img
            src="/leaf-plant-svgrepo-com.png"
            className="w-12 h-12 md:w-24 md:h-24"
          />
          <p className="mt-6 text-justify md:px-14">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusantium
            nam enim rerum deserunt quod aut veniam obcaecati soluta expedita et
            non distinctio ducimus repellat deleniti quaerat tenetur perferendis
            officia officiis in modi labore, pariatur a.
          </p>
        </div>
        <div className="flex flex-col items-center mt-12 md:w-1/3">
          <img src="/share-culture.png" className="w-12 h-12 md:w-24 md:h-24" />
          <p className="mt-6 text-justify md:px-14">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Sint quam
            delectus, quasi facilis facere praesentium consequuntur voluptatem,
            similique placeat neque illum veniam. Accusantium qui maxime
            temporibus porro nulla illo eveniet eum repellendus dolores possimus
            repellat necessitatibus, minima numquam, molestiae laborum.
          </p>
        </div>
      </div>
    </div>
  );
};
export default Main;
