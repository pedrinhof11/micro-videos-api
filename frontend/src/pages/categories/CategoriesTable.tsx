import * as React from 'react';
import MUIDataTable, { MUIDataTableColumnDef} from "mui-datatables";
import {useEffect, useState} from "react";
import {httpVideo} from "../../http";
import {Chip} from "@material-ui/core";

const columns: MUIDataTableColumnDef[] = [
  { name: "name", label: "Nome" },
  {
    name: "is_active",
    label: "Ativo?",
    options: {
      customBodyRender: (value, meta) => {
        return value ? <Chip label="Sim"/> : <Chip label="Não"/>;
      }
    }
  },
  { name: "created_at", label: "Criado em" }
];

export default () => {

  const [categories, setCategories] = useState([])

  // eslint-disable-next-line
  useEffect(() => {
    const fetchData = async () => {
      const {data: { data }} = await httpVideo.get("categories");
      setCategories(data);
    }
    fetchData()
  }, [])

  return (
    <MUIDataTable
      title="Categorias"
      columns={columns}
      data={categories}
    />
  );
};