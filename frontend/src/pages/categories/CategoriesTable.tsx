import * as React from 'react';
import {useEffect, useState} from "react";
import MUIDataTable, {MUIDataTableColumnDef} from "mui-datatables";
import {Chip} from "@material-ui/core";
import {dateFormatFromIso} from '../../utils';
import CategoryResource from '../../http/CategoryResource';
import {Category} from '../../types/models';

const columns: MUIDataTableColumnDef[] = [
  {name: "name", label: "Nome"},
  {
    name: "is_active",
    label: "Ativo?",
    options: {
      customBodyRender: (value) => {
        return value as any ? <Chip label="Sim" color="primary" /> : <Chip label="Não" color="secondary" />;
      }
    }
  },
  { 
    name: "created_at",
    label: "Criado em",
    options: {
      customBodyRender: (value) => {
      return <span>{dateFormatFromIso(value, 'dd/MM/yyyy')}</span>
      }
    } 
  }
];

const CategoriesTable = () => {

  const [categories, setCategories] = useState<Category[]>([])

  const fetchData = async () => {
    const {data: {data}} = await CategoryResource.list();
    setCategories(data);
  }

  useEffect(() => {
    fetchData()
  })

  return (
    <MUIDataTable
      title="Categorias"
      columns={columns}
      data={categories}
    />
  );
};

export default CategoriesTable