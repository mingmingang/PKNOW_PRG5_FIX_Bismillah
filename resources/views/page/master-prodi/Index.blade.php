import { useState } from "react";
import Footer from "../../backbone/Footer";
import Header from "../../backbone/Header";
import budi from "../../../assets/fotobudi.png";
import ceweVR from "../../../assets/ceweVR_beranda.png";
import cowoTop from "../../../assets/cowoTop_beranda.png";
import BerandaProdi from "../../backbone/BerandaProdi";
import prodiBackground1 from "../../../assets/BackBeranda/BackgroundTPM.png";
import prodiBackground2 from "../../../assets/BackBeranda/BackgroundTRPL.png";

export default function Prodi() {
  const dropdownContent = [
    { title: "PIC Kelompok Keahlian", icon: "fas fa-users" },
    { title: "Persetujuan Anggota Keahlian", icon: "fas fa-check" },
  ];

  const currentDateTime = new Date().toLocaleString("id-ID", {
    dateStyle: "long",
    timeStyle: "short",
  });

  const userProfile = {
    name: "Arie Kusumawati",
    role: "Program Studi",
    lastLogin: currentDateTime,
    photo: budi,
  };

  const slidesData = [
    {
      title: "System Knowledge Management System",
      background: prodiBackground1,
      mascot: ceweVR,
    },
    {
      title: "Learning System",
      background: prodiBackground2,
      mascot: cowoTop,
    },
  ];

  const selectedProgram = "Program1"; // Replace this with actual program logic

  const programBackgrounds = {
    Program1: prodiBackground1,
  };

  return (
    <>
      <div className="app-container">
        <Header
          showMenu={true}
          dropdownContent={dropdownContent}
          userProfile={userProfile}
          menuItems={["beranda", "knowledge_database", "i_learning"]}
          beranda="/beranda_prodi"
        />
        <main>
          <BerandaProdi slidesData={slidesData} />
        </main>
        <Footer />
      </div>
    </>
  );
}
