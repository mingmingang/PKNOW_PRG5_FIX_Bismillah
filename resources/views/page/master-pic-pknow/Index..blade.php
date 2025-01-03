import { useState } from "react";
import Header from "../../backbone/Header";
import budi from "../../../assets/fotobudi.png";
import Footer from "../../backbone/Footer";
import "../../../style/Beranda.css";
import BerandaUtama from "../../backbone/BerandaUtama";

export default function PICPKNOW() {
  const dropdownContent = [
    { title: "Kelola Kelompok Keahlian", icon: "fas fa-cogs" },
    { title: "Kelola Anggota", icon: "fas fa-users" },
  ];

  const dropdownKnowledge = [
    { title: "Daftar Pustaka", icon: "fas fa-book" }
  ]

  const currentDateTime = new Date().toLocaleString("id-ID", {
    dateStyle: "long",
    timeStyle: "short",
  });

  const userProfile = {
    name: "Budi Hartono",
    role: "PIC P-KNOW",
    lastLogin: currentDateTime,
    photo: budi,
  };

  return (
    <div className="app-container">
      <Header
        showMenu={true}
        dropdownContent={dropdownContent}
        userProfile={userProfile}
        menuItems={["beranda","kelompok_keahlian", "knowledge_database", "i_learning"]}
        dropdownKnowledge={dropdownKnowledge}
      />
      <main>
        <BerandaUtama />
      </main>
      <Footer />
    </div>
  );
}
