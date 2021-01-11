import React from "react";
import { useParams } from "react-router-dom";
import Page from "../../components/Page";
import CastMembersForm from "./CastMembersForm";

const CastMembersPageForm = () => {
  const { id } = useParams<{ id: string }>();
  return (
    <Page title={!id ? "Criar Membro de Elenco" : "Editar Membro de Elenco"}>
      <CastMembersForm />
    </Page>
  );
};

export default CastMembersPageForm;
