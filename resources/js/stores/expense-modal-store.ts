import { Budget } from "@/types/budget";
import { Category } from "@/utills/category";
import { create } from "zustand";

type ExpenseModalStore = {
    open: boolean;
    budget: Budget | null;
    categories: Category[];
    openCreateMotal: () => void;
    closeModal: () => void;
    setBudget: (budget: Budget) => void;
    setCategories: (categories: Category[]) => void;
};

export const useExpenseModalStore = create<ExpenseModalStore>((set) => ({
    open: false,
    budget: null,
    categories: [],
    openCreateMotal: () => {
        set({
            open: true,
        });
    },
    closeModal: () => {
        set({
            open: false,
        });
    },
    setBudget: (budget: Budget) => {
        set({
            budget, // budget: budget
        });
    },
    setCategories: (categories) => {
        set({
            categories,
        });
    },
}));
