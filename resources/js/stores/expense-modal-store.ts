import { create } from "zustand";

type ExpenseModalStore = {
    open: boolean;
    openCreateMotal: () => void;
    closeModal: () => void;
};

export const useExpenseModalStore = create<ExpenseModalStore>((set) => ({
    open: false,
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
}));
